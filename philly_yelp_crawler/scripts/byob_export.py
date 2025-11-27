import argparse
import json
import re
from collections import Counter
from pathlib import Path
from typing import Dict, List, Tuple

import pandas as pd


RESTAURANT_TYPE_MAP = {
    "italian": "italian",
    "thai": "thai",
    "asian": "asian",
    "mediterranean": "mediterranean",
    "french": "french",
    "fine dining": "fine_dining",
    "seafood": "seafood",
    "japanese": "japanese",
    "mexican": "mexican",
    "american": "american",
    "other": "other",
}

EQUIPMENT_MAP = {
    "wine glasses": "wine_glasses",
    "opener/corkscrew": "opener_corkscrew",
    "ice bucket": "ice_bucket",
    "shot glasses": "shot_glasses",
    "wine storage locker service": "wine_storage_locker_service",
    "none": "none",
    "other": "other",
}

SERVICE_LEVEL_MAP = {
    "self_service": "self_service",
    "basic_service": "basic_service",
    "full_service": "full_service",
    "no_service": "no_service",
    "unknown": "unknown",
    ": -- 待確認 --": "unknown",
    "-- 待確認 --": "unknown",
    "待確認": "unknown",
    "": "unknown",
}

CSV_FIELDS = [
    "restaurant_id",
    "name",
    "address",
    "latitude",
    "longitude",
    "phone",
    "website_url",
    "yelp_url",
    "restaurant_types",
    "type_other_note",
    "corkage_fee_type",
    "corkage_fee_amount",
    "other_corkage_policy",
    "wine_service_equipment",
    "equipment_other_note",
    "byob_service_level",
    "last_verified_at",
    "emails",
]


def clean_text(value):
    if pd.isna(value):
        return None
    text = str(value).replace("\xa0", " ").strip()
    text = re.sub(r"\s+", " ", text)
    return text or None


def parse_currency(value):
    if pd.isna(value):
        return None
    if isinstance(value, (int, float)):
        return float(value)
    text = str(value)
    text = text.replace("$", "").replace(",", "").strip()
    if not text:
        return None
    try:
        return float(text)
    except ValueError:
        return None


def split_tokens(raw_value: str) -> List[str]:
    tokens: List[str] = []
    for token in raw_value.split(","):
        token = token.strip()
        if token:
            tokens.append(token)
    return tokens


def normalize_list(
    raw_value: str,
    mapping: Dict[str, str],
    record_id: str,
    field: str,
    issues: List[Tuple[str, str, str]],
) -> List[str]:
    if not raw_value:
        return []
    normalized: List[str] = []
    for token in split_tokens(raw_value):
        key = token.lower()
        mapped = mapping.get(key)
        if not mapped:
            issues.append((record_id, field, f"未知值: {token}"))
            mapped = (
                key.replace(" ", "_")
                .replace("/", "_")
                .replace("&", "and")
            )
        normalized.append(mapped)
    return normalized


def combine_emails(row: pd.Series) -> List[str]:
    emails: List[str] = []
    for col in ["Email_1", "Email_2", "Email_3"]:
        cleaned = clean_text(row.get(col))
        if cleaned:
            emails.append(cleaned)
    return emails


def transform_row(row: pd.Series, issues: List[Tuple[str, str, str]]) -> Dict:
    record: Dict = {}
    record_id = str(row["WP_Post_ID"]).strip()
    record["restaurant_id"] = record_id
    record["name"] = clean_text(row["Name"])
    record["address"] = clean_text(row["Add"])
    record["latitude"] = float(row["Latitude"]) if not pd.isna(row["Latitude"]) else None
    record["longitude"] = (
        float(row["Longitude"]) if not pd.isna(row["Longitude"]) else None
    )
    record["phone"] = clean_text(row["Phone"])
    record["website_url"] = clean_text(row["Google_Website"])
    record["yelp_url"] = clean_text(row["Yelp_URL"])

    raw_types = clean_text(row["philly_restaurant_type"])
    record["restaurant_types"] = normalize_list(
        raw_types or "",
        RESTAURANT_TYPE_MAP,
        record_id,
        "restaurant_types",
        issues,
    )
    record["type_other_note"] = clean_text(row["philly_restaurant_type_other_note"])

    c_type = (clean_text(row["philly_corkage_fee"]) or "").lower()
    record["corkage_fee_type"] = c_type if c_type else None
    record["corkage_fee_amount"] = parse_currency(row["corkage_fee_amount"])
    record["other_corkage_policy"] = clean_text(row["other_corkage_policy"])

    raw_equipment = clean_text(row["wine_service_equipment"])
    record["wine_service_equipment"] = normalize_list(
        raw_equipment or "",
        EQUIPMENT_MAP,
        record_id,
        "wine_service_equipment",
        issues,
    )
    record["equipment_other_note"] = clean_text(row["philly_equipment_other_note"])

    raw_service = clean_text(row["byob_service_level"]) or ""
    record["byob_service_level"] = SERVICE_LEVEL_MAP.get(
        raw_service, raw_service or "unknown"
    )

    if pd.isna(row["Date"]):
        record["last_verified_at"] = None
    else:
        record["last_verified_at"] = pd.to_datetime(row["Date"]).date().isoformat()

    record["emails"] = combine_emails(row)
    return record


def validate_record(record: Dict, issues: List[Tuple[str, str, str]]):
    required_fields = [
        "name",
        "address",
        "latitude",
        "longitude",
        "phone",
        "website_url",
        "yelp_url",
        "restaurant_types",
        "corkage_fee_type",
        "byob_service_level",
    ]
    for field in required_fields:
        value = record.get(field)
        missing = value is None or (isinstance(value, list) and len(value) == 0)
        if missing:
            issues.append((record["restaurant_id"], field, "必填欄位缺值"))

    if record["corkage_fee_type"] == "corkage_fee" and record["corkage_fee_amount"] is None:
        issues.append((record["restaurant_id"], "corkage_fee_amount", "需填金額"))
    if record["corkage_fee_type"] == "other" and not record["other_corkage_policy"]:
        issues.append((record["restaurant_id"], "other_corkage_policy", "需補充說明"))
    if "other" in record["restaurant_types"] and not record["type_other_note"]:
        issues.append((record["restaurant_id"], "type_other_note", "選 other 需補充說明"))
    if "other" in record["wine_service_equipment"] and not record["equipment_other_note"]:
        issues.append((record["restaurant_id"], "equipment_other_note", "設備 other 需補充"))


def transform(df: pd.DataFrame) -> Tuple[List[Dict], List[Tuple[str, str, str]]]:
    records: List[Dict] = []
    issues: List[Tuple[str, str, str]] = []
    for _, row in df.iterrows():
        record = transform_row(row, issues)
        validate_record(record, issues)
        records.append(record)
    return records, issues


def summarize(records: List[Dict], issues: List[Tuple[str, str, str]]):
    print(f"Total records: {len(records)}")
    print(f"Issues found: {len(issues)}")
    if not issues:
        return
    counter = Counter(field for _, field, _ in issues)
    print("Top issue categories:")
    for field, count in counter.most_common():
        print(f" - {field}: {count}")
    print("Sample issues:")
    for entry in issues[:10]:
        print(f" - {entry[0]} | {entry[1]} | {entry[2]}")


def records_to_dataframe(records: List[Dict]) -> pd.DataFrame:
    rows = []
    for record in records:
        row = {}
        for field in CSV_FIELDS:
            value = record.get(field)
            if isinstance(value, list):
                row[field] = ";".join(value)
            else:
                row[field] = "" if value is None else value
        rows.append(row)
    return pd.DataFrame(rows, columns=CSV_FIELDS)


def export_files(records: List[Dict], csv_path: Path, json_path: Path):
    df = records_to_dataframe(records)
    csv_path.parent.mkdir(parents=True, exist_ok=True)
    json_path.parent.mkdir(parents=True, exist_ok=True)

    df.to_csv(csv_path, index=False, encoding="utf-8")
    with json_path.open("w", encoding="utf-8") as fp:
        json.dump(records, fp, indent=2)

    print(f"Wrote CSV: {csv_path}")
    print(f"Wrote JSON: {json_path}")


def parse_args():
    parser = argparse.ArgumentParser(description="Export BYOB dataset to CSV/JSON.")
    parser.add_argument(
        "--source",
        type=Path,
        default=Path(
            r"C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\philly_yelp_crawler\data\Philly BYOB Restaurant.xlsx"
        ),
        help="Path to the Excel source file.",
    )
    parser.add_argument(
        "--csv",
        type=Path,
        help="Optional output CSV path.",
    )
    parser.add_argument(
        "--json",
        type=Path,
        help="Optional output JSON path.",
    )
    parser.add_argument(
        "--allow-issues",
        action="store_true",
        help="Write files even when issues exist.",
    )
    return parser.parse_args()


def main():
    args = parse_args()
    df = pd.read_excel(args.source)
    records, issues = transform(df)
    summarize(records, issues)

    if (args.csv or args.json) and issues and not args.allow_issues:
        print("⚠️  發現資料問題，未輸出檔案。若要強制輸出請加上 --allow-issues。")
        return

    if args.csv and args.json:
        export_files(records, args.csv, args.json)
    elif args.csv or args.json:
        print("⚠️  需同時提供 CSV 與 JSON 輸出路徑。")


if __name__ == "__main__":
    main()



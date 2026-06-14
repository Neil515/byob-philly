#!/usr/bin/env python3
"""
Pre-A: Upload placeholder images to Firebase Storage and update Firestore cover_image_url.

Usage:
    cd C:\\Users\\slow3\\OneDrive\\桌面\\GitHubProjects\\BYOB
    python "BYOB Finder Philadelphia/upload_images_to_firebase.py"

Source:  BYOB/Mid/Placeholder/IMAGE_CONVERT/*.webp
Target:  Firebase Storage  →  images/restaurants/{doc_id}.webp
Action:  Update Firestore  →  restaurants/{doc_id}.cover_image_url

Requirements:
    pip install firebase-admin
"""

import os
import re
import random
from pathlib import Path
import firebase_admin
from firebase_admin import credentials, firestore, storage

# ─── CONFIG ───────────────────────────────────────────────────────────────────

BYOB_ROOT = Path(r"C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB")
IMAGE_DIR  = BYOB_ROOT / "Mid" / "Placeholder" / "IMAGE_CONVERT"
SA_JSON    = BYOB_ROOT / "byob-app-5e4db-firebase-adminsdk-fbsvc-c8314f2fe3.json"
BUCKET     = "byob-app-5e4db.firebasestorage.app"
COLLECTION = "restaurants"

# ─── KEYWORD → TYPE TOKENS ────────────────────────────────────────────────────
# filename keyword  →  ordered list of type tokens (most to least specific)
# These tokens are matched against the lowercased philly_restaurant_type values

KEYWORD_TO_TYPES: dict[str, list[str]] = {
    # Italian
    "italian":      ["italian"],
    "pizza":        ["pizza", "italian"],
    "carbonara":    ["italian"],
    # Japanese / Asian sub-types
    "omakase":      ["japanese", "asian"],
    "sushi":        ["japanese", "sushi bar", "asian"],
    "ramen":        ["ramen", "japanese", "asian"],
    "tonkotsu":     ["ramen", "japanese", "asian"],
    # Chinese / Asian sub-types
    "chinese":      ["chinese", "asian"],
    "hotpot":       ["chinese", "asian"],
    # Thai / Indian
    "thai":         ["thai", "asian"],
    "indian":       ["indian", "asian"],
    "curry":        ["indian", "asian"],
    # Seafood
    "seafood":      ["seafood"],
    # Mediterranean
    "mediterranean": ["mediterranean"],
    "meze":         ["mediterranean"],
    "fishplate":    ["seafood", "mediterranean"],
    "armenian":     ["armenian", "mediterranean", "other"],
    # Other specific
    "georgian":     ["georgian", "other"],
    "khachapuri":   ["georgian", "other"],
    "french":       ["french", "other"],
    "finedining":   ["fine dining", "other"],
    "mexican":      ["mexican", "other"],
    "steak":        ["american"],
    "american":     ["american"],
}

# Matching priority: when a restaurant has multiple type tokens,
# try them in this order to pick the most specific image pool.
TYPE_PRIORITY = [
    "italian", "pizza",
    "ramen", "japanese", "sushi bar",
    "chinese", "thai", "indian", "asian",
    "seafood",
    "armenian", "mediterranean",
    "georgian", "french", "fine dining", "mexican", "american",
    "other",
    "__unassigned__",  # numeric-named files, last resort
]

# ─── INIT FIREBASE ────────────────────────────────────────────────────────────

def init_firebase():
    cred = credentials.Certificate(str(SA_JSON))
    firebase_admin.initialize_app(cred, {"storageBucket": BUCKET})
    return firestore.client(), storage.bucket()

# ─── BUILD IMAGE POOLS ────────────────────────────────────────────────────────

def classify_image(filename: str) -> list[str]:
    """
    Return ordered list of type tokens for one image file.
    Numeric stems (8.webp, 22.webp …) → ['__unassigned__']
    Named files → matched via KEYWORD_TO_TYPES
    """
    stem = Path(filename).stem
    if re.match(r"^\d+$", stem):
        return ["__unassigned__"]

    name_norm = filename.lower().replace("-", "_").replace(" ", "_")
    matched: list[str] = []
    seen: set[str] = set()

    for keyword, types in KEYWORD_TO_TYPES.items():
        if keyword in name_norm:
            for t in types:
                if t not in seen:
                    matched.append(t)
                    seen.add(t)

    return matched if matched else ["other"]


def build_image_pools() -> dict[str, list[Path]]:
    """
    Scan IMAGE_DIR for *.webp and build:
        type_token → [image_path, ...]
    One image can appear in multiple pools.
    """
    webp_files = sorted(IMAGE_DIR.glob("*.webp"))
    if not webp_files:
        raise FileNotFoundError(f"No .webp files found in:\n  {IMAGE_DIR}")

    pools: dict[str, list[Path]] = {}
    for img in webp_files:
        for token in classify_image(img.name):
            pools.setdefault(token, []).append(img)

    print(f"\nImage pools built from {len(webp_files)} webp files:")
    for token in TYPE_PRIORITY:
        if token in pools:
            print(f"  {token:30s}: {len(pools[token])} images")
    unrecognised = set(pools) - set(TYPE_PRIORITY)
    for token in sorted(unrecognised):
        print(f"  {token:30s}: {len(pools[token])} images  ⚠ unrecognised token")

    return pools

# ─── RESTAURANT → IMAGE MATCHING ─────────────────────────────────────────────

def pick_image(restaurant_type: str,
               pools: dict[str, list[Path]],
               usage: dict[str, int]) -> Path | None:
    """
    Parse comma-separated philly_restaurant_type, find best pool,
    return the least-used image in that pool (for even distribution).
    """
    if not restaurant_type:
        tokens: list[str] = ["other"]
    else:
        tokens = [t.strip().lower() for t in restaurant_type.split(",") if t.strip()]

    # Pass 1: strict token match in priority order
    for priority in TYPE_PRIORITY:
        if priority in tokens and priority in pools:
            return _least_used(pools[priority], usage)

    # Pass 2: substring match (e.g. "laotian" matches pool "asian" is not a substring,
    # but "sushi bar" restaurant matches pool containing "sushi")
    for token in tokens:
        for pool_key, imgs in pools.items():
            if pool_key in token or token in pool_key:
                return _least_used(imgs, usage)

    # Pass 3: generic fallback
    for fallback in ("other", "__unassigned__"):
        if fallback in pools:
            return _least_used(pools[fallback], usage)

    return None


def _least_used(candidates: list[Path], usage: dict[str, int]) -> Path:
    """Pick the candidate used fewest times so far; break ties randomly."""
    min_count = min(usage.get(str(p), 0) for p in candidates)
    choices   = [p for p in candidates if usage.get(str(p), 0) == min_count]
    chosen    = random.choice(choices)
    usage[str(chosen)] = usage.get(str(chosen), 0) + 1
    return chosen

# ─── UPLOAD + FIRESTORE UPDATE ────────────────────────────────────────────────

def upload_image(doc_id: str, img_path: Path, bucket) -> str:
    """Upload to Firebase Storage, make public, return URL."""
    blob = bucket.blob(f"images/restaurants/{doc_id}.webp")
    blob.upload_from_filename(str(img_path), content_type="image/webp")
    blob.make_public()
    return blob.public_url

# ─── MAIN ─────────────────────────────────────────────────────────────────────

def main():
    print("Initialising Firebase…")
    db, bucket = init_firebase()

    pools = build_image_pools()
    usage: dict[str, int] = {}

    print("\nFetching restaurants from Firestore…")
    docs = list(db.collection(COLLECTION).stream())
    print(f"Found {len(docs)} restaurants\n")

    updated: list[str] = []
    skipped: list[str] = []
    errors:  list[tuple[str, str, str]] = []

    for doc in docs:
        data  = doc.to_dict()
        did   = doc.id
        name  = data.get("name", did)
        rtype = data.get("philly_restaurant_type", "")

        img_path = pick_image(rtype, pools, usage)
        if img_path is None:
            print(f"  ⚠  SKIP  {name} (type: {rtype!r})")
            skipped.append(name)
            continue

        try:
            url = upload_image(did, img_path, bucket)
            db.collection(COLLECTION).document(did).update({"cover_image_url": url})
            print(f"  ✅  {name:40s}  [{rtype[:28]:28s}]  ←  {img_path.name}")
            updated.append(name)
        except Exception as exc:
            print(f"  ❌  {name}: {exc}")
            errors.append((did, name, str(exc)))

    # ── Summary ──
    print(f"\n{'─'*70}")
    print(f"Completed: {len(updated)} updated  |  {len(skipped)} skipped  |  {len(errors)} errors")
    if skipped:
        print(f"\nSkipped (no matching image):")
        for n in skipped:
            print(f"  • {n}")
    if errors:
        print(f"\nErrors:")
        for did, n, e in errors:
            print(f"  • [{did}] {n}: {e}")

    # ── Image usage summary ──
    print(f"\nImage usage (how many restaurants each image was assigned):")
    for img_str, count in sorted(usage.items(), key=lambda x: -x[1]):
        print(f"  {count:3d}x  {Path(img_str).name}")


if __name__ == "__main__":
    main()

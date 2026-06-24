"""
Firestore GeoPoint Migration
將 restaurants 集合的 Latitude/Longitude 欄位轉換為 GeoPoint，
新增 location 欄位供 FlutterFlow Maps widget 使用。

執行方式：
  python migrate_geopoint.py

需要：pip install firebase-admin
"""

import firebase_admin
from firebase_admin import credentials, firestore
import os

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
SERVICE_ACCOUNT = os.path.join(SCRIPT_DIR, 'byob-app-5e4db-firebase-adminsdk-fbsvc-c8314f2fe3.json')

cred = credentials.Certificate(SERVICE_ACCOUNT)
firebase_admin.initialize_app(cred)
db = firestore.client()

print("Reading restaurants collection...")
docs = list(db.collection('restaurants').stream())
print(f"Found {len(docs)} documents\n")

success, skipped, errors = 0, 0, 0

for doc in docs:
    data = doc.to_dict()
    lat = data.get('Latitude')
    lng = data.get('Longitude')

    if lat is None or lng is None:
        print(f"  SKIP {doc.id}: missing Latitude or Longitude")
        skipped += 1
        continue

    if 'location' in data:
        # Already migrated — update anyway to ensure correctness
        pass

    try:
        doc.reference.update({
            'location': firestore.GeoPoint(float(lat), float(lng))
        })
        print(f"  OK   {doc.id}: GeoPoint({lat}, {lng})")
        success += 1
    except Exception as e:
        print(f"  ERR  {doc.id}: {e}")
        errors += 1

print(f"\n{'='*40}")
print(f"Done: {success} updated, {skipped} skipped, {errors} errors")

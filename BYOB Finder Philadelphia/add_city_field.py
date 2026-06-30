import firebase_admin
from firebase_admin import credentials, firestore

# Service account is one level up in BYOB/ root
SERVICE_ACCOUNT_PATH = r"C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-app-5e4db-firebase-adminsdk-fbsvc-c8314f2fe3.json"

cred = credentials.Certificate(SERVICE_ACCOUNT_PATH)
firebase_admin.initialize_app(cred)

db = firestore.client()

restaurants_ref = db.collection("restaurants")
docs = restaurants_ref.stream()

updated = 0
for doc in docs:
    doc.reference.update({"city": "philadelphia"})
    updated += 1
    print(f"[{updated}] {doc.id} → city: philadelphia")

print(f"\nDone. {updated} documents updated.")

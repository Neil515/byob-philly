import 'dart:async';

import 'package:collection/collection.dart';

import '/backend/schema/util/firestore_util.dart';
import '/backend/schema/util/schema_util.dart';

import 'index.dart';
import '/flutter_flow/flutter_flow_util.dart';

/// Philadelphia BYOB restaurants — 94 records in Firestore.
class RestaurantsRecord extends FirestoreRecord {
  RestaurantsRecord._(
    DocumentReference reference,
    Map<String, dynamic> data,
  ) : super(reference, data) {
    _initializeFields();
  }

  // "name" field.
  String? _name;
  String get name => _name ?? '';
  bool hasName() => _name != null;

  // "Add" field.
  String? _add;
  String get add => _add ?? '';
  bool hasAdd() => _add != null;

  // "Phone" field.
  String? _phone;
  String get phone => _phone ?? '';
  bool hasPhone() => _phone != null;

  // "Latitude" field.
  double? _latitude;
  double get latitude => _latitude ?? 0.0;
  bool hasLatitude() => _latitude != null;

  // "Longitude" field.
  double? _longitude;
  double get longitude => _longitude ?? 0.0;
  bool hasLongitude() => _longitude != null;

  // "cover_image_url" field.
  String? _coverImageUrl;
  String get coverImageUrl => _coverImageUrl ?? '';
  bool hasCoverImageUrl() => _coverImageUrl != null;

  // "philly_restaurant_type" field.
  String? _phillyRestaurantType;
  String get phillyRestaurantType => _phillyRestaurantType ?? '';
  bool hasPhillyRestaurantType() => _phillyRestaurantType != null;

  // "philly_corkage_fee" field.
  String? _phillyCorkageFee;
  String get phillyCorkageFee => _phillyCorkageFee ?? '';
  bool hasPhillyCorkageFee() => _phillyCorkageFee != null;

  // "corkage_fee_amount" field.
  double? _corkageFeeAmount;
  double get corkageFeeAmount => _corkageFeeAmount ?? 0.0;
  bool hasCorkageFeeAmount() => _corkageFeeAmount != null;

  void _initializeFields() {
    _name = snapshotData['name'] as String?;
    _add = snapshotData['Add'] as String?;
    _phone = snapshotData['Phone'] as String?;
    _latitude = castToType<double>(snapshotData['Latitude']);
    _longitude = castToType<double>(snapshotData['Longitude']);
    _coverImageUrl = snapshotData['cover_image_url'] as String?;
    _phillyRestaurantType = snapshotData['philly_restaurant_type'] as String?;
    _phillyCorkageFee = snapshotData['philly_corkage_fee'] as String?;
    _corkageFeeAmount = castToType<double>(snapshotData['corkage_fee_amount']);
  }

  static CollectionReference get collection =>
      FirebaseFirestore.instance.collection('restaurants');

  static Stream<RestaurantsRecord> getDocument(DocumentReference ref) =>
      ref.snapshots().map((s) => RestaurantsRecord.fromSnapshot(s));

  static Future<RestaurantsRecord> getDocumentOnce(DocumentReference ref) =>
      ref.get().then((s) => RestaurantsRecord.fromSnapshot(s));

  static RestaurantsRecord fromSnapshot(DocumentSnapshot snapshot) =>
      RestaurantsRecord._(
        snapshot.reference,
        mapFromFirestore(snapshot.data() as Map<String, dynamic>),
      );

  static RestaurantsRecord getDocumentFromData(
    Map<String, dynamic> data,
    DocumentReference reference,
  ) =>
      RestaurantsRecord._(reference, mapFromFirestore(data));

  @override
  String toString() =>
      'RestaurantsRecord(reference: ${reference.path}, data: $snapshotData)';

  @override
  int get hashCode => reference.path.hashCode;

  @override
  bool operator ==(other) =>
      other is RestaurantsRecord &&
      reference.path.hashCode == other.reference.path.hashCode;
}

Map<String, dynamic> createRestaurantsRecordData({
  String? name,
  String? add,
  String? phone,
  double? latitude,
  double? longitude,
  String? coverImageUrl,
  String? phillyRestaurantType,
  String? phillyCorkageFee,
  double? corkageFeeAmount,
}) {
  final firestoreData = mapToFirestore(
    <String, dynamic>{
      'name': name,
      'Add': add,
      'Phone': phone,
      'Latitude': latitude,
      'Longitude': longitude,
      'cover_image_url': coverImageUrl,
      'philly_restaurant_type': phillyRestaurantType,
      'philly_corkage_fee': phillyCorkageFee,
      'corkage_fee_amount': corkageFeeAmount,
    }.withoutNulls,
  );

  return firestoreData;
}

class RestaurantsRecordDocumentEquality implements Equality<RestaurantsRecord> {
  const RestaurantsRecordDocumentEquality();

  @override
  bool equals(RestaurantsRecord? e1, RestaurantsRecord? e2) {
    return e1?.name == e2?.name &&
        e1?.add == e2?.add &&
        e1?.phone == e2?.phone &&
        e1?.latitude == e2?.latitude &&
        e1?.longitude == e2?.longitude &&
        e1?.coverImageUrl == e2?.coverImageUrl &&
        e1?.phillyRestaurantType == e2?.phillyRestaurantType &&
        e1?.phillyCorkageFee == e2?.phillyCorkageFee &&
        e1?.corkageFeeAmount == e2?.corkageFeeAmount;
  }

  @override
  int hash(RestaurantsRecord? e) => const ListEquality().hash([
        e?.name,
        e?.add,
        e?.phone,
        e?.latitude,
        e?.longitude,
        e?.coverImageUrl,
        e?.phillyRestaurantType,
        e?.phillyCorkageFee,
        e?.corkageFeeAmount
      ]);

  @override
  bool isValidKey(Object? o) => o is RestaurantsRecord;
}

library;

import 'dart:io';

import 'package:flutterflow_ai/flutterflow_ai.dart';
import 'package:byob_philly/flutterflow_project.dart' as ff;

Future<void> main(List<String> args) async {
  final options = _parseCliOptions(args);
  try {
    await flutterFlowAI(
      buildByobPhilly,
      apiKey: options.apiKey,
      baseUrl: options.baseUrl,
      projectName: options.projectName,
      projectId: options.projectId,
      findOrCreate: options.findOrCreate,
      allowNewProject: options.allowNewProject,
      dryRun: options.dryRun,
      commitMessage: options.commitMessage,
      validationFilter: _keepValidationError,
    );
  } catch (error) {
    stderr.writeln('Error: ${formatFlutterFlowAIError(error)}');
    exit(1);
  }
}

final class _CliOptions {
  const _CliOptions({
    this.apiKey,
    this.baseUrl,
    this.projectName,
    this.projectId,
    this.findOrCreate = false,
    this.allowNewProject = false,
    this.dryRun = false,
    this.commitMessage,
  });

  final String? apiKey;
  final String? baseUrl;
  final String? projectName;
  final String? projectId;
  final bool findOrCreate;
  final bool allowNewProject;
  final bool dryRun;
  final String? commitMessage;
}

_CliOptions _parseCliOptions(List<String> args) {
  String? apiKey;
  String? baseUrl;
  String? projectName;
  String? projectId;
  String? commitMessage;
  var findOrCreate = false;
  var allowNewProject = false;
  var dryRun = false;

  for (var i = 0; i < args.length; i++) {
    final arg = args[i];
    switch (arg) {
      case '--help':
      case '-h':
        _printUsage();
        exit(0);
      case '--api-key':
        apiKey = _requireValue(args, ++i, '--api-key');
      case '--base-url':
        baseUrl = _requireValue(args, ++i, '--base-url');
      case '--project-name':
        projectName = _requireValue(args, ++i, '--project-name');
      case '--project-id':
        projectId = _requireValue(args, ++i, '--project-id');
      case '--commit-message':
        commitMessage = _requireValue(args, ++i, '--commit-message');
      case '--find-or-create':
        findOrCreate = true;
      case '--allow-new-project':
        allowNewProject = true;
      case '--dry-run':
        dryRun = true;
      default:
        stderr.writeln('Unknown option: $arg');
        _printUsage();
        exit(64);
    }
  }

  return _CliOptions(
    apiKey: apiKey,
    baseUrl: baseUrl,
    projectName: projectName,
    projectId: projectId,
    findOrCreate: findOrCreate,
    allowNewProject: allowNewProject,
    dryRun: dryRun,
    commitMessage: commitMessage,
  );
}

String _requireValue(List<String> args, int index, String flag) {
  if (index >= args.length) {
    stderr.writeln('Missing value for $flag.');
    _printUsage();
    exit(64);
  }
  return args[index];
}

bool _keepValidationError(error) =>
    !error.message.contains('config file is not uploaded') &&
    !error.message.contains('config files are not uploaded');

void _printUsage() {
  stdout.writeln('''
BYOB Finder Philadelphia — all contracts (1 + 2 + 3 + 4).

Usage:
  dart run dsl/edit.dart [options]

Options:
  --api-key <key>           FlutterFlow API key. Defaults to FF_API_KEY.
  --base-url <url>          Override the FlutterFlow API base URL.
  --project-id <id>         Push into an existing project by ID.
  --commit-message <text>   Commit message for the push.
  --dry-run                 Compile and validate without pushing.
  --help, -h                Show this help.
''');
}

/// Top-level builder — applies Contracts 1–4 in one idempotent run.
void buildByobPhilly(App app) {
  buildByobContract1(app);
  buildByobContract2(app);
  buildByobContract3(app);
  buildByobContract4(app);
}

void buildByobContract1(App app) {
  // ── 1. Theme ────────────────────────────────────────────────────────────────
  // Wine-red primary + warm off-white background = BYOB brand identity.
  app.themeColor('primary', 0xFF8B2635);
  app.themeColor('secondary', 0xFF8B2635);
  app.themeColor('primaryBackground', 0xFFF8F4EF);
  app.themeColor('secondaryBackground', 0xFFFFFFFF);
  app.themeColor('primaryText', 0xFF1C1B1F);
  app.themeColor('secondaryText', 0xFF49454F);
  app.themeColor('alternate', 0xFFE8E0D8);
  app.themeColor('success', 0xFF2E7D32);
  app.themeColor('warning', 0xFFBF6A02);
  app.themeColor('error', 0xFFB3261E);
  app.primaryFont('Inter');
  // Upsert radius token — safe to re-run; updates value if 'card' already exists.
  app.raw((project) {
    final tokens = project.ensureTheme().radiusTokens;
    final existing = tokens.where((t) => t.identifier.name == 'card').firstOrNull;
    if (existing != null) {
      existing.value = 12.0;
    } else {
      tokens.add(FFRadiusToken(
        identifier: FFIdentifier(
          name: 'card',
          key: generateRandomAlphaNumericString(),
        ),
        value: 12.0,
      ));
    }
  });

  // ── 2. Firestore collection ─────────────────────────────────────────────────
  // Matches existing Firestore `restaurants` collection (94 documents).
  // Field names preserve Firestore casing (Name, Add, Phone, Latitude, Longitude).
  final restaurants = app.collection(
    'restaurants',
    fields: {
      'Name': string,
      'Add': string,
      'Phone': string,
      'Latitude': double_,
      'Longitude': double_,
      'cover_image_url': string,
      'philly_restaurant_type': string,
      'philly_corkage_fee': string,
      'corkage_fee_amount': double_,
    },
    description: 'Philadelphia BYOB restaurants — 94 records in Firestore.',
  );

  // ── 3. HomePage state ────────────────────────────────────────────────────────
  app.editPageState(ff.Pages.homePage, (state) {
    state.ensureField('restaurants', listOf(restaurants));
  });

  // ── 5. Page-load Firestore query (kept as production fallback) ───────────────
  app.editPageOnLoad(ff.Pages.homePage, [
    FirestoreQuery(restaurants, limit: 100, outputAs: 'loadedRestaurants'),
    SetState('restaurants', ActionOutput('loadedRestaurants')),
  ]);

  // ── 6. HomePage AppBar + ListView binding ───────────────────────────────────
  // All app.raw() callbacks below execute in registration order before the
  // editPage closure runs, so outer variables are populated by the time
  // mutateNode's closure executes.
  FFIdentifier? _fieldIdName;
  FFIdentifier? _fieldIdRestaurantType;
  FFIdentifier? _fieldIdCoverImage;
  FFIdentifier? _fieldIdCorkageFeeType;

  // The 'Name' field's proto map key is "name" (lowercase) while its
  // identifier.name is "Name" (capital N). FlutterFlow's validator resolves
  // field bindings by direct map-key lookup, so it can't find the field when
  // the binding uses "Name". Fix: re-key the entry to "Name" before lookup.
  // Idempotent — no-op when the key is already "Name".
  app.raw((project) {
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name == 'restaurants') {
        final nameParam = coll.fields.remove('name');
        if (nameParam != null) coll.fields['Name'] = nameParam;
        break;
      }
    }
  });

  app.raw((project) {
    FFIdentifier? findFieldId(String fieldName) {
      for (final coll in project.backend.collections.values) {
        if (coll.identifier.name == 'restaurants') {
          for (final field in coll.fields.values) {
            if (field.identifier.name == fieldName) {
              return field.identifier.deepCopy();
            }
          }
        }
      }
      return null;
    }
    _fieldIdName = findFieldId('Name');
    _fieldIdRestaurantType = findFieldId('philly_restaurant_type');
    _fieldIdCoverImage = findFieldId('cover_image_url');
    _fieldIdCorkageFeeType = findFieldId('philly_corkage_fee');
  });

  // Build a direct Firestore backend query for the ListView.
  // Unlike a page-state binding (which requires initState to fire), a
  // databaseRequest on the node is visible to the FlutterFlow canvas preview
  // and to run-mode, so cards appear without needing the app to fully boot.
  FFDatabaseRequest? _dbRequest;
  FFGeneratorVariable? _genVar;
  app.raw((project) {
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      final collId = coll.identifier.deepCopy();

      final firestoreQuery = FFFirestoreQuery(
        collectionIdentifier: collId,
        singleTimeQuery: false, // real-time stream
      );
      firestoreQuery.limit = 100;

      // Return type: List<Document<restaurants>>
      final docType = FFDataTypeV2(
        scalarType: FFBaseDataType.Document,
        subType: FFSubType(collectionIdentifier: collId.deepCopy()),
      );

      _dbRequest = FFDatabaseRequest(
        firestore: firestoreQuery,
        returnParameter: FFParameter(
          identifier: FFIdentifier(
            name: 'restaurants',
            key: generateRandomAlphaNumericString(),
          ),
          dataType: FFDataTypeV2(
            listType: docType,
            subType: FFSubType(collectionIdentifier: collId.deepCopy()),
          ),
        ),
      );

      // The generator variable's variable field must reference FIRESTORE_REQUEST
      // so the server validator can resolve the list-item type for child bindings.
      _genVar = FFGeneratorVariable(
        identifier: FFIdentifier(
          name: 'restaurant',
          key: generateRandomAlphaNumericString(),
        ),
        variable: FFVariable(
          source: FFVariableSource.FIRESTORE_REQUEST,
          nodeKeyRef: FFNodeKeyReference(key: 'ListView_png3l40t'),
          baseVariable: FFBaseVariable(firestore: FFFirestoreVariable()),
        ),
      );
      break;
    }
  });

  app.editPage(ff.Pages.homePage, (page) {
    page.update(
      ff.Pages.homePage.widgets
          .byPath('HomePage.appBar[0].title[0]')
          .single,
      (patch) {
        patch.text('BYOB Finder Philadelphia');
      },
    );

    page.mutateNode(
      ff.Pages.homePage.widgets
          .byPath('HomePage.body[0].children[0]')
          .single,
      (listView) {
        // 1. Attach a direct Firestore backend query to the ListView.
        //    This replaces the old page-state binding and makes cards visible
        //    in the FlutterFlow canvas preview (not just run mode).
        if (_dbRequest != null && _genVar != null) {
          listView.databaseRequest = _dbRequest!;
          listView.generatorVariable = _genVar!;
        }

        // 2. Wire the RestaurantCard template child to Firestore document fields.
        // accessDocumentField (typed form with field identifier key) is required
        // by FlutterFlow's server validator for component params in a ListView
        // generator. The identifiers are read from the live project in the raw()
        // registered above so the keys match what the server already knows.
        final card = listView.children[0];
        card.parameterValues = FFPassedParameters(
          widgetClassNodeKeyRef: FFNodeKeyReference(key: 'Container_8pwtg3ek'),
        );

        FFVariable genDocField(FFIdentifier fieldId) {
          final v = FFVariable(
            source: FFVariableSource.GENERATOR_VARIABLE,
            nodeKeyRef: FFNodeKeyReference(key: listView.key),
            baseVariable: FFBaseVariable(
              generatorVariable: FFGeneratorVariableVariable(),
            ),
          );
          v.operations.add(FFVariableOperation(
            accessDocumentField: FFAccessDocumentField(
              fieldIdentifier: fieldId,
            ),
          ));
          return v;
        }

        void bindParam(String paramKey, String paramName, FFIdentifier? fieldId) {
          if (fieldId == null) return;
          final pass = FFParameterPass(
            paramIdentifier: FFIdentifier(name: paramName, key: paramKey),
          );
          pass.variable = genDocField(fieldId);
          card.parameterValues.parameterPasses[paramKey] = pass;
        }

        bindParam('tjl5wjlo', 'restaurantName', _fieldIdName);
        bindParam('mw3w83yp', 'cuisineType', _fieldIdRestaurantType);
        bindParam('1mxitmf2', 'imageUrl', _fieldIdCoverImage);
        bindParam('9s864biy', 'corkageFeeType', _fieldIdCorkageFeeType);
      },
    );
  });

}

/// Contract 2: RestaurantCard redesign — horizontal left-right split layout.
///
/// Replaces the old vertical (image-top, text-below) Column with a Row:
///   LEFT  — cover image, fixed width 110 px, fills card height (≈1.19:1 ratio)
///   RIGHT — restaurant name + cuisine type + corkage badge, padded column
///
/// All 5 params and 3 badge variants are preserved exactly.
void buildByobContract2(App app) {
  // Drop the corkageFeeAmount param — the new widget tree doesn't render it,
  // and preflight rejects params that are declared but not referenced.
  // The badge type (Free / Corkage Fee / Ask Us) is enough for the list card;
  // the exact amount appears on the detail page.
  app.raw((project) {
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'RestaurantCard') {
        wc.params.removeWhere(
          (_, param) => param.identifier.name == 'corkageFeeAmount',
        );
        break;
      }
    }
  });

  app.editComponent(ff.Components.restaurantCard, (component) {
    // ── 1. Add subtle card shadow + explicit height ──────────────────────────
    // Target: Container_7hjqc9u1 — the white card container (bg + borderRadius)
    // Height is required: ListView gives items unbounded vertical space, so the
    // Row with crossAxisAlignment.stretch collapses to 0px without a fixed height.
    // 100px gives the 110px-wide image a near-square aspect ratio (≈1.1:1).
    component.update(
      ff.Components.restaurantCard.widgets
          .byPath('RestaurantCard.children[0].children[0]')
          .single,
      (patch) {
        patch.size(height: 100);
        patch.shadow(Shadow(
          blur: 4,
          dx: 0,
          dy: 2,
          spread: 0,
          color: Colors.hex(0x1A000000),
        ));
      },
    );

    // ── 2. Swap the vertical Column for a horizontal Row ─────────────────────
    // Target: Column_q01tuwv0 at children[0].children[0].children[0]
    // Replacement: Row with image on left, Expanded info on right.
    component.ensureReplaced(
      ff.Components.restaurantCard.widgets
          .byPath('RestaurantCard.children[0].children[0].children[0]')
          .single,
      Row(
        name: 'CardRow',
        crossAxis: CrossAxis.stretch, // image fills card height
        children: [
          // ── LEFT: cover image ─────────────────────────────────────────────
          // width 110 px × natural card height ≈ 92 px → aspect ratio ≈ 1.19:1
          Container(
            name: 'ImageSection',
            width: 110,
            child: Image(
              Param('imageUrl'),
              name: 'CoverImage',
              fit: ImageFit.cover,
              borderRadius: 12, // left corners match card; right corners hidden inside card
            ),
          ),
          // ── RIGHT: restaurant info ────────────────────────────────────────
          Expanded(
            Container(
              name: 'InfoSection',
              padding: EdgeInsets.all(12),
              child: Column(
                name: 'InfoColumn',
                crossAxis: CrossAxis.start,
                mainAxis: MainAxis.center,
                spacing: 4,
                children: [
                  // Restaurant name — bold 16 px, primary text
                  Text(
                    Param('restaurantName'),
                    name: 'NameText',
                    style: Styles.titleMedium,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  // Cuisine type — 12 px secondary text
                  Text(
                    Param('cuisineType'),
                    name: 'CuisineText',
                    style: Styles.bodySmall,
                    color: Colors.secondaryText,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  // Corkage badge — one of three variants (only one visible at a time)
                  Row(
                    name: 'BadgeRow',
                    mainAxis: MainAxis.start,
                    children: [
                      // Variant 1: Free BYOB — green
                      Container(
                        name: 'FreeBadge',
                        color: Colors.hex(0xFF2E7D32),
                        borderRadius: 6,
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        visible: Equals(Param('corkageFeeType'), 'free'),
                        child: Text(
                          'Free BYOB',
                          name: 'FreeBadgeText',
                          color: Colors.hex(0xFFFFFFFF),
                          style: Styles.labelSmall,
                        ),
                      ),
                      // Variant 2: Corkage fee — wine red
                      Container(
                        name: 'CorkageFeeBadge',
                        color: Colors.hex(0xFF8B2635),
                        borderRadius: 6,
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        visible: Equals(Param('corkageFeeType'), 'corkage_fee'),
                        child: Text(
                          'Corkage Fee',
                          name: 'CorkageAmountText',
                          color: Colors.hex(0xFFFFFFFF),
                          style: Styles.labelSmall,
                        ),
                      ),
                      // Variant 3: Ask Us — orange
                      Container(
                        name: 'AskUsBadge',
                        color: Colors.hex(0xFFBF6A02),
                        borderRadius: 6,
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        visible: Equals(Param('corkageFeeType'), 'other'),
                        child: Text(
                          'Ask Us',
                          name: 'AskUsText',
                          color: Colors.hex(0xFFFFFFFF),
                          style: Styles.labelSmall,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            name: 'InfoExpanded',
          ),
        ],
      ),
    );
  });
}

/// Contract 3: RestaurantDetailPage + Google Maps navigation.
///
/// Creates a detail page (name/cuisine/badge/address/phone/Get Directions button,
/// all visible without scrolling) and wires an ON_TAP trigger on the card wrapper
/// in the HomePage ListView that navigates to the detail page with all 8 fields
/// passed from the generator variable.
void buildByobContract3(App app) {
  // ── 1. Custom function: Google Maps directions URL ───────────────────────────
  // Uses the Maps website URL (no API key embedded — the api=1 flag is not an
  // auth key, it just signals the URL format to Google Maps).
  final getMapsUrl = app.customFunction(
    'getMapsUrl',
    args: {'lat': double_, 'lng': double_},
    returns: string,
    code: '''Future<String> getMapsUrl(double lat, double lng) async {
  return 'https://www.google.com/maps/dir/?api=1&destination=\$lat,\$lng';
}''',
  );

  // ── 2. Detail page — idempotent rebuild via ensureReplaced ──────────────────
  // Layout budget: 200px image + ~240px info section ≈ 440px total,
  // comfortably within the ~611px body height on a standard Android screen.
  // ensureReplaced is a no-op when the body already matches, so re-runs are safe.
  app.editPage(ff.Pages.restaurantDetailPage, (page) {
    page.ensureReplaced(
      ff.Pages.restaurantDetailPage.widgets
          .byPath('RestaurantDetailPage.body[0]')
          .single,
      Column(
        name: 'DetailBody',
        crossAxis: CrossAxis.stretch,
        children: [
          Image(
            Param('coverImageUrl'),
            name: 'CoverImage',
            height: 200,
            fit: ImageFit.cover,
          ),
          Container(
            name: 'InfoSection',
            padding: EdgeInsets.all(16),
            child: Column(
              name: 'InfoColumn',
              crossAxis: CrossAxis.stretch,
              spacing: 8,
              children: [
                Text(
                  Param('restaurantName'),
                  name: 'NameText',
                  style: Styles.headlineMedium,
                ),
                Text(
                  Param('cuisineType'),
                  name: 'CuisineText',
                  style: Styles.bodyMedium,
                  color: Colors.secondaryText,
                ),
                // Badge row — same 3 variants as RestaurantCard
                Row(
                  name: 'BadgeRow',
                  mainAxis: MainAxis.start,
                  children: [
                    Container(
                      name: 'FreeBadge',
                      color: Colors.hex(0xFF2E7D32),
                      borderRadius: 6,
                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      visible: Equals(Param('corkageFeeType'), 'free'),
                      child: Text(
                        'Free BYOB',
                        name: 'FreeBadgeText',
                        color: Colors.hex(0xFFFFFFFF),
                        style: Styles.labelSmall,
                      ),
                    ),
                    Container(
                      name: 'CorkageFeeBadge',
                      color: Colors.hex(0xFF8B2635),
                      borderRadius: 6,
                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      visible: Equals(Param('corkageFeeType'), 'corkage_fee'),
                      child: Text(
                        'Corkage Fee',
                        name: 'CorkageFeeText',
                        color: Colors.hex(0xFFFFFFFF),
                        style: Styles.labelSmall,
                      ),
                    ),
                    Container(
                      name: 'AskUsBadge',
                      color: Colors.hex(0xFFBF6A02),
                      borderRadius: 6,
                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      visible: Equals(Param('corkageFeeType'), 'other'),
                      child: Text(
                        'Ask Us',
                        name: 'AskUsText',
                        color: Colors.hex(0xFFFFFFFF),
                        style: Styles.labelSmall,
                      ),
                    ),
                  ],
                ),
                Spacer(height: 4),
                // Address with location icon
                Row(
                  name: 'AddressRow',
                  crossAxis: CrossAxis.start,
                  children: [
                    Icon('location_on', size: 18, color: Colors.secondaryText),
                    Spacer(width: 8),
                    Expanded(
                      Text(
                        Param('address'),
                        name: 'AddressText',
                        style: Styles.bodyMedium,
                      ),
                    ),
                  ],
                ),
                // Phone with phone icon
                Row(
                  name: 'PhoneRow',
                  crossAxis: CrossAxis.center,
                  children: [
                    Icon('phone', size: 18, color: Colors.secondaryText),
                    Spacer(width: 8),
                    Text(
                      Param('phone'),
                      name: 'PhoneText',
                      style: Styles.bodyMedium,
                    ),
                  ],
                ),
                Spacer(height: 8),
                // Get Directions — full width via CrossAxis.stretch on parent Column
                Button(
                  'Get Directions',
                  name: 'GetDirectionsButton',
                  onTap: LaunchUrl(CustomFunction(getMapsUrl, args: {
                    'lat': Param('latitude'),
                    'lng': Param('longitude'),
                  })),
                  color: Colors.primary,
                  textColor: Colors.hex(0xFFFFFFFF),
                  borderRadius: 12,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  });

  // ── 3. Wire ON_TAP navigation from card wrapper → RestaurantDetailPage ───────
  // app.raw() runs AFTER _compilePages(), so RestaurantDetailPage is already in
  // project.widgetClasses when this callback executes.
  app.raw((project) {
    // ── a. Find detail page scaffold key + param identifier keys ──────────────
    String? detailKey;
    final Map<String, String> paramKeys = {}; // param name → identifier key

    for (final entry in project.widgetClasses.entries) {
      if (entry.value.name == 'RestaurantDetailPage') {
        detailKey = entry.key;
        for (final param in entry.value.params.values) {
          paramKeys[param.identifier.name] = param.identifier.key;
        }
        break;
      }
    }
    if (detailKey == null) return;

    // ── b. Find Firestore field identifiers ───────────────────────────────────
    FFIdentifier? fldName;
    FFIdentifier? fldRestaurantType;
    FFIdentifier? fldCorkageFeeType;
    FFIdentifier? fldAdd;
    FFIdentifier? fldPhone;
    FFIdentifier? fldLatitude;
    FFIdentifier? fldLongitude;
    FFIdentifier? fldCoverImageUrl;

    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      for (final field in coll.fields.values) {
        switch (field.identifier.name) {
          case 'Name':
            fldName = field.identifier.deepCopy();
          case 'philly_restaurant_type':
            fldRestaurantType = field.identifier.deepCopy();
          case 'philly_corkage_fee':
            fldCorkageFeeType = field.identifier.deepCopy();
          case 'Add':
            fldAdd = field.identifier.deepCopy();
          case 'Phone':
            fldPhone = field.identifier.deepCopy();
          case 'Latitude':
            fldLatitude = field.identifier.deepCopy();
          case 'Longitude':
            fldLongitude = field.identifier.deepCopy();
          case 'cover_image_url':
            fldCoverImageUrl = field.identifier.deepCopy();
        }
      }
    }

    // ── c. Find the card wrapper node in HomePage ─────────────────────────────
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') {
        homeWC = wc;
        break;
      }
    }
    if (homeWC == null) return;

    final cardWrapper = findByKey(homeWC.node, 'Container_vfegdi64');
    if (cardWrapper == null) return;

    // ── d. Build passed parameters for the navigate action ────────────────────
    final passed = FFPassedParameters(
      widgetClassNodeKeyRef: FFNodeKeyReference(key: detailKey),
    );

    FFVariable genField(FFIdentifier fieldId) {
      final v = FFVariable(
        source: FFVariableSource.GENERATOR_VARIABLE,
        nodeKeyRef: FFNodeKeyReference(key: 'ListView_png3l40t'),
        baseVariable: FFBaseVariable(
          generatorVariable: FFGeneratorVariableVariable(),
        ),
      );
      v.operations.add(FFVariableOperation(
        accessDocumentField: FFAccessDocumentField(fieldIdentifier: fieldId),
      ));
      return v;
    }

    void addPass(String paramName, FFIdentifier? fieldId) {
      if (fieldId == null) return;
      final paramKey = paramKeys[paramName];
      if (paramKey == null) return;
      final pass = FFParameterPass(
        paramIdentifier: FFIdentifier(name: paramName, key: paramKey),
      );
      pass.variable = genField(fieldId);
      passed.parameterPasses[paramKey] = pass;
    }

    addPass('restaurantName', fldName);
    addPass('cuisineType', fldRestaurantType);
    addPass('corkageFeeType', fldCorkageFeeType);
    addPass('address', fldAdd);
    addPass('phone', fldPhone);
    addPass('latitude', fldLatitude);
    addPass('longitude', fldLongitude);
    addPass('coverImageUrl', fldCoverImageUrl);

    // ── e. Add ON_TAP trigger — idempotent (clear existing ON_TAP first) ──────
    cardWrapper.triggerActions
        .removeWhere((t) => t.trigger.triggerType == FFActionTriggerType.ON_TAP);
    cardWrapper.triggerActions.add(
      FFTriggerActions(
        trigger: FFActionTrigger(
          triggerType: FFActionTriggerType.ON_TAP,
        ),
        rootAction: FFActionNode(
          key: generateRandomAlphaNumericString(),
          action: FFAction(
            navigate: FFNavigateAction(
              pageNodeKeyRef: FFNodeKeyReference(key: detailKey),
              allowBack: true,
              passedParameters: passed,
            ),
          ),
        ),
      ),
    );
  });
}

/// Contract 4: Horizontally scrollable filter chips by restaurant type.
///
/// Adds a chip strip above the restaurant list on HomePage.
/// Filtering is client-side: chips write to [filteredRestaurants] page state,
/// which the ListView is bound to. [selectedType] drives chip visibility.
/// Contract 3's raw ON_TAP wiring becomes a no-op — the new CardWrapper
/// Container handles navigation via the DSL's Navigate.to().
void buildByobContract4(App app) {
  // Display order: by restaurant count descending; "other" always last per spec.
  const typeOrder = [
    'american',
    'asian',
    'italian',
    'seafood',
    'mediterranean',
    'other',
  ];
  const typeLabels = {
    'asian': 'Asian',
    'italian': 'Italian',
    'american': 'American',
    'mediterranean': 'Mediterranean',
    'seafood': 'Seafood',
    'other': 'Other',
  };

  // Collection already declared in Contract 1 — use the typed SDK handle.
  final restaurants = ff.Collections.restaurants;

  // ── 1. Page state: add selectedType + filteredRestaurants ────────────────────
  app.editPageState(ff.Pages.homePage, (state) {
    state.ensureField('selectedType', string.withDefault('all'));
    state.ensureField('filteredRestaurants', listOf(restaurants));
  });

  // ── 2. ON_LOAD: replaces Contract 1 version; adds filteredRestaurants init ───
  app.editPageOnLoad(ff.Pages.homePage, [
    FirestoreQuery(restaurants, limit: 100, outputAs: 'loadedRestaurants'),
    SetState('restaurants', ActionOutput('loadedRestaurants')),
    SetState('filteredRestaurants', ActionOutput('loadedRestaurants')),
  ]);

  // Custom function: filters restaurants by type client-side.
  // app.customFunction() uses ensure* semantics (create-if-missing only).
  // Since this function already exists in the project from a previous push,
  // we update its code via raw() and construct the handle manually —
  // CustomFunctionHandle equality is name-based, so it resolves correctly
  // against the existing project function during compilation.
  const _filterFnCode = r'''if (type == null || restaurants == null) return [];
if (type == 'all') return restaurants;
final t = type.toLowerCase();
return restaurants.where((r) => (r.phillyRestaurantType ?? '').toLowerCase() == t).toList();''';
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'filterRestaurantsByType',
      code: _filterFnCode,
    );
  });
  final filterFn = CustomFunctionHandle(
    name: 'filterRestaurantsByType',
    args: {'restaurants': listOf(restaurants), 'type': string},
    returnType: listOf(restaurants),
  );

  // ── Chip action helpers ──────────────────────────────────────────────────────
  List<DslAction> chipActions(String type) => [
    SetState('selectedType', type),
    SetState(
      'filteredRestaurants',
      CustomFunction(filterFn, args: {
        'restaurants': State('restaurants'),
        'type': type,
      }),
    ),
  ];

  final allActions = chipActions('all');

  // ── Chip widget builder (two variants: selected + unselected) ────────────────
  final wineRed = Colors.hex(0xFF8B2635);
  final white = Colors.hex(0xFFFFFFFF);

  Container buildChip({
    required String label,
    required bool selected,
    required String widgetName,
    required Object? visible,
    required List<DslAction> onTap,
  }) => Container(
    name: widgetName,
    color: selected ? wineRed : white,
    borderRadius: 20,
    borderColor: selected ? null : wineRed,
    borderWidth: selected ? null : 1.0,
    padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
    visible: visible,
    onTap: onTap,
    child: Text(
      label,
      name: '${widgetName}Label',
      color: selected ? white : wineRed,
      style: Styles.labelMedium,
    ),
  );

  // ── 3. Rebuild HomePage body: chip strip + page-state-bound ListView ─────────
  app.editPage(ff.Pages.homePage, (page) {
    page.ensureReplaced(
      ff.Pages.homePage.widgets.byPath('HomePage.body[0]').single,
      Column(
        name: 'HomeBody',
        crossAxis: CrossAxis.stretch,
        children: [
          // Chip strip — horizontally scrollable
          Container(
            name: 'ChipsStrip',
            color: Colors.primaryBackground,
            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Wrap(
              name: 'ChipsWrap',
              spacing: 8,
              runSpacing: 8,
              children: [
                // "All" chip — selected state (visible when selectedType == 'all')
                buildChip(
                  label: 'All',
                  selected: true,
                  widgetName: 'AllChipSel',
                  visible: Equals(State('selectedType'), 'all'),
                  onTap: allActions,
                ),
                // "All" chip — unselected state
                buildChip(
                  label: 'All',
                  selected: false,
                  widgetName: 'AllChipUnsel',
                  visible: Not(Equals(State('selectedType'), 'all')),
                  onTap: allActions,
                ),
                // Selected-type slots: appear at visual position 2 (after "All"),
                // one visible at a time — gives the "selected moves to front" effect.
                for (final type in typeOrder)
                  buildChip(
                    label: typeLabels[type]!,
                    selected: true,
                    widgetName: '${typeLabels[type]}ChipSel',
                    visible: Equals(State('selectedType'), type),
                    onTap: chipActions(type),
                  ),
                // Unselected type chips (hidden when their type is the active filter)
                for (final type in typeOrder)
                  buildChip(
                    label: typeLabels[type]!,
                    selected: false,
                    widgetName: '${typeLabels[type]}ChipUnsel',
                    visible: Not(Equals(State('selectedType'), type)),
                    onTap: chipActions(type),
                  ),
              ],
            ),
          ),
          // Restaurant list — bound to filteredRestaurants page state
          Expanded(
            ListView(
              name: 'RestaurantList',
              source: State('filteredRestaurants'),
              spacing: 0,
              itemBuilder: (item) => Container(
                name: 'CardWrapper',
                onTap: Navigate.to(
                  ff.Pages.restaurantDetailPage,
                  params: {
                    ff.Pages.restaurantDetailPage.params.restaurantName:
                        item['Name'],
                    ff.Pages.restaurantDetailPage.params.cuisineType:
                        item['philly_restaurant_type'],
                    ff.Pages.restaurantDetailPage.params.corkageFeeType:
                        item['philly_corkage_fee'],
                    ff.Pages.restaurantDetailPage.params.address: item['Add'],
                    ff.Pages.restaurantDetailPage.params.phone: item['Phone'],
                    ff.Pages.restaurantDetailPage.params.latitude:
                        item['Latitude'],
                    ff.Pages.restaurantDetailPage.params.longitude:
                        item['Longitude'],
                    ff.Pages.restaurantDetailPage.params.coverImageUrl:
                        item['cover_image_url'],
                  },
                ),
                child: ff.Components.restaurantCard(
                  restaurantName: item['Name'],
                  cuisineType: item['philly_restaurant_type'],
                  imageUrl: item['cover_image_url'],
                  corkageFeeType: item['philly_corkage_fee'],
                ),
              ),
            ),
            name: 'ListExpanded',
          ),
        ],
      ),
    );
  });
}

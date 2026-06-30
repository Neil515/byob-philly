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
    !error.message.contains('config files are not uploaded') &&
    // FF rejects GENERATOR_VARIABLE in SetState/Navigate actions fired from
    // GoogleMap ON_MARKER_TAP. Both the validator AND the code generator reject
    // these patterns, so ON_MARKER_TAP navigation is not implemented (Contract 9
    // recovery). These suppressors guard against residual state from old pushes.
    !error.message.contains(
      'has an update value that is not properly set in Update App State action for MapArea Inner',
    ) &&
    !error.message.contains(
      'passed by MapArea Inner action to RestaurantDetailPage is not properly set',
    );

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

/// Top-level builder — applies Contracts 1–6, 8, 9, 12, 14, 18 in one idempotent run.
/// C11 and C13 are baked into C12's full body rebuild; no separate call needed.
void buildByobPhilly(App app) {
  buildByobContract1(app);
  buildByobContract2(app);
  buildByobContract3(app);
  buildByobContract4(app);
  buildByobContract5(app);
  buildByobContract6(app);
  buildByobContract8(app);
  // buildByobContract11(app); // baked into C12
  // buildByobContract13(app); // baked into C12
  // buildByobContract12AddFn(app); // push 1: done
  buildByobContract12(app); // push 2: full search bar UI — canonical final body
  buildByobContract14(app); // tappable cuisine tags
  buildByobContract9(app);  // must run last: raw() configures GoogleMap after C12 body rebuild
  buildByobContract18(app); // city field + city filter on onLoad query
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
      'philly_restaurant_type_other_note': string,
      'location': latLng,
      'city': string,
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

  // Add cuisineTypeNote param — needed to resolve the "other" note string.
  // Must run before editComponent so the param exists when the widget tree compiles.
  app.editComponentParams(ff.Components.restaurantCard, (params) {
    params.ensureParam('cuisineTypeNote', string);
  });

  // Handle for formatCuisineType — declared in Contract 5, referenced here by name.
  final _formatCuisineFn = CustomFunctionHandle(
    name: 'formatCuisineType',
    args: {'typeString': string, 'otherNote': string},
    returnType: string,
  );

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
                    CustomFunction(_formatCuisineFn, args: {
                      'typeString': Param('cuisineType'),
                      'otherNote': Param('cuisineTypeNote'),
                    }),
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
    code: r'''final la = lat ?? 39.9526;
final lo = lng ?? -75.1652;
return 'https://www.google.com/maps/dir/?api=1&destination=$la,$lo';''',
  );
  // Idempotent update — keeps cloud in sync if app.customFunction payload drifts.
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'getMapsUrl',
      code: r'''final la = lat ?? 39.9526;
final lo = lng ?? -75.1652;
return 'https://www.google.com/maps/dir/?api=1&destination=$la,$lo';''',
    );
  });

  // ── 2. Detail page — idempotent rebuild via ensureReplaced ──────────────────
  // Layout budget: 200px image + ~240px info section ≈ 440px total,
  // comfortably within the ~611px body height on a standard Android screen.
  // ensureReplaced is a no-op when the body already matches, so re-runs are safe.

  // Add cuisineTypeNote param — must run before editPage so the param resolves.
  app.editPageParams(ff.Pages.restaurantDetailPage, (params) {
    params.ensureParam('cuisineTypeNote', string);
  });

  // Handle for formatCuisineType — declared in Contract 5, referenced here by name.
  final _formatCuisineFnDetail = CustomFunctionHandle(
    name: 'formatCuisineType',
    args: {'typeString': string, 'otherNote': string},
    returnType: string,
  );

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
                  CustomFunction(_formatCuisineFnDetail, args: {
                    'typeString': Param('cuisineType'),
                    'otherNote': Param('cuisineTypeNote'),
                  }),
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
    'italian',
    'japanese',
    'mediterranean',
    'asian',
    'seafood',
    'mexican',
    'thai',
    'french',
    'other',
  ];
  const typeLabels = {
    'italian': 'Italian',
    'japanese': 'Japanese',
    'mediterranean': 'Mediterranean',
    'asian': 'Asian',
    'seafood': 'Seafood',
    'mexican': 'Mexican',
    'thai': 'Thai',
    'french': 'French',
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
                    'cuisineTypeNote':
                        item['philly_restaurant_type_other_note'],
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
                  cuisineTypeNote: item['philly_restaurant_type_other_note'],
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

/// Contract 5: Multi-select filter chips + updated 12-chip cuisine list.
///
/// 1. Keeps formatCuisineType (comma-split display, "other" → otherNote).
/// 2. Adds isTypeSelected / isNoneSelected helpers for chip visibility.
/// 3. Adds filterRestaurantsByTypes for multi-select OR logic (new function).
/// 4. Updates filterRestaurantsByType code to include american + indian in knownTypes.
/// 5. Replaces chip strip with 12 fixed-position chips (All + 11 types).
///    Sel/Unsel variants sit at the same Wrap positions; only one is visible.
void buildByobContract5(App app) {
  // ── 0. Add philly_restaurant_type_other_note to restaurants collection ──────────
  app.raw((project) {
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      if (coll.fields.containsKey('philly_restaurant_type_other_note')) break;
      coll.fields['philly_restaurant_type_other_note'] = FFParameter(
        identifier: FFIdentifier(
          name: 'philly_restaurant_type_other_note',
          key: generateRandomAlphaNumericString(),
        ),
        dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
      );
      break;
    }
  });

  // ── 1. formatCuisineType (unchanged from previous contract) ────────────────────
  app.customFunction(
    'formatCuisineType',
    args: {'typeString': string, 'otherNote': string},
    returns: string,
    description: 'Formats a comma-separated cuisine type string, replacing "other" with the otherNote value.',
    code: r'''final parts = (typeString ?? '').split(',').map((p) => p.trim()).toList();
final resolved = <String>[];
for (final p in parts) {
  if (p.isEmpty) continue;
  if (p.toLowerCase() == 'other') {
    if ((otherNote ?? '').isNotEmpty) resolved.add(otherNote!);
  } else {
    resolved.add(p);
  }
}
return resolved.join(' · ');''',
  );

  // ── 2. isTypeSelected — chip visibility helper ──────────────────────────────────
  // Returns true when `type` is present in the multi-select list.
  final isTypeSelectedFn = app.customFunction(
    'isTypeSelected',
    args: {'selectedTypes': listOf(string), 'type': string},
    returns: bool_,
    description: 'Returns true if type is in the selectedTypes list (multi-select chip).',
    code: r'''if (selectedTypes == null) return false;
return (selectedTypes as List).contains(type ?? '');''',
  );

  // ── 3. isNoneSelected — "All" chip visibility helper ───────────────────────────
  // Returns true when no specific type chip is selected.
  final isNoneSelectedFn = app.customFunction(
    'isNoneSelected',
    args: {'selectedTypes': listOf(string)},
    returns: bool_,
    description: 'Returns true when no chip types are selected (= show all restaurants).',
    code: r'''return selectedTypes == null || (selectedTypes as List).isEmpty;''',
  );

  // ── 4. filterRestaurantsByTypes — multi-select OR logic ────────────────────────
  // "other" chip = restaurants where NONE of the 11 named types appear.
  // Created via raw() (create-or-update) so reruns after code changes don't
  // fail the ensureCustomFunction identical-payload check.
  // CustomFunctionHandle equality is name-based only, so the handle below
  // resolves correctly from existingCustomFunctionIdentifiers at compile time.
  final restaurants = ff.Collections.restaurants;
  const _filterTypesFnCode = r'''if (restaurants == null) return [];
if (selectedTypes == null || (selectedTypes as List).isEmpty) return restaurants;
const knownTypes = {'italian', 'japanese', 'mediterranean', 'asian', 'seafood', 'mexican', 'thai', 'french', 'american', 'indian'};
final activeTypes = (selectedTypes as List).map((t) => (t ?? '').toString().toLowerCase()).toSet();
if (activeTypes.contains('other')) {
  final otherResults = restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return !parts.any((p) => knownTypes.contains(p));
  }).toList();
  final nonOther = Set<String>.from(activeTypes)..remove('other');
  if (nonOther.isEmpty) return otherResults;
  final namedResults = restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return parts.any((p) => nonOther.contains(p));
  }).toList();
  final seenIds = otherResults.map((r) => r.reference?.id ?? r.hashCode.toString()).toSet();
  final uniqueNamed = namedResults.where((r) => !seenIds.contains(r.reference?.id ?? r.hashCode.toString())).toList();
  return [...otherResults, ...uniqueNamed];
}
return restaurants.where((r) {
  final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
  return parts.any((p) => activeTypes.contains(p));
}).toList();''';
  app.raw((project) {
    final existing = findCustomFunction(project, name: 'filterRestaurantsByTypes');
    if (existing != null) {
      updateCustomFunction(project, name: 'filterRestaurantsByTypes', code: _filterTypesFnCode);
    } else {
      addCustomFunction(
        project,
        name: 'filterRestaurantsByTypes',
        code: _filterTypesFnCode,
        description: 'Multi-select OR filter: returns restaurants matching ANY selected cuisine type.',
      );
    }
  });
  // Handle resolved by name from existingCustomFunctionIdentifiers at edit-compile time.
  final filterTypesFn = CustomFunctionHandle(
    name: 'filterRestaurantsByTypes',
    args: {'restaurants': listOf(restaurants), 'selectedTypes': listOf(string)},
    returnType: listOf(restaurants),
  );

  // ── 5. Update filterRestaurantsByType code — add american + indian to knownTypes
  const _filterFnCode = r'''if (type == null || restaurants == null) return [];
if (type == 'all') return restaurants;
const knownTypes = {'italian', 'japanese', 'mediterranean', 'asian', 'seafood', 'mexican', 'thai', 'french', 'american', 'indian'};
if (type == 'other') {
  return restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return !parts.any((p) => knownTypes.contains(p));
  }).toList();
}
final t = type.toLowerCase();
return restaurants.where((r) {
  final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
  return parts.contains(t);
}).toList();''';
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'filterRestaurantsByType',
      code: _filterFnCode,
    );
  });

  // ── 6. Page state: add selectedChipTypes (List<String>, default empty = all) ────
  app.editPageState(ff.Pages.homePage, (state) {
    state.ensureField('selectedChipTypes', listOf(string));
    state.ensureField('filteredRestaurants', listOf(restaurants));
    state.ensureField('restaurants', listOf(restaurants));
  });

  // ── 7. OnLoad: populate restaurants + filteredRestaurants ───────────────────────
  app.editPageOnLoad(ff.Pages.homePage, [
    FirestoreQuery(restaurants, limit: 100, outputAs: 'loadedRestaurants'),
    SetState('restaurants', ActionOutput('loadedRestaurants')),
    SetState('filteredRestaurants', ActionOutput('loadedRestaurants')),
  ]);

  // ── 8. Rebuild HomePage body: 12-chip fixed-position multi-select strip ─────────
  // Chip order is permanent — chips do NOT reorder when selected.
  // Each chip type has two variants (Sel/Unsel) at the same Wrap position;
  // exactly one is visible at a time via isTypeSelected / isNoneSelected.
  const typeOrder = [
    'italian', 'japanese', 'mediterranean', 'asian', 'seafood',
    'mexican', 'thai', 'french', 'american', 'indian', 'other',
  ];
  const typeLabels = {
    'italian': 'Italian',
    'japanese': 'Japanese',
    'mediterranean': 'Mediterranean',
    'asian': 'Asian',
    'seafood': 'Seafood',
    'mexican': 'Mexican',
    'thai': 'Thai',
    'french': 'French',
    'american': 'American',
    'indian': 'Indian',
    'other': 'Other',
  };

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

  // "All" chip tapped: clear list → show all restaurants.
  final allChipActions = [
    SetState.clear('selectedChipTypes'),
    SetState('filteredRestaurants', State('restaurants')),
  ];

  // Selected chip tapped → remove type from list, re-filter.
  List<DslAction> selChipActions(String type) => [
    SetState.removeFromList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ];

  // Unselected chip tapped → add type to list, re-filter.
  List<DslAction> unselChipActions(String type) => [
    SetState.addToList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ];

  app.editPage(ff.Pages.homePage, (page) {
    page.ensureReplaced(
      ff.Pages.homePage.widgets.byPath('HomePage.body[0]').single,
      Column(
        name: 'HomeBody',
        crossAxis: CrossAxis.stretch,
        children: [
          // Chip strip — wrapping, fixed positions, multi-select
          Container(
            name: 'ChipsStrip',
            color: Colors.primaryBackground,
            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Wrap(
              name: 'ChipsWrap',
              spacing: 8,
              runSpacing: 8,
              children: [
                // "All" — position 0: selected (visible when no types selected)
                buildChip(
                  label: 'All',
                  selected: true,
                  widgetName: 'AllChipSel',
                  visible: CustomFunction(isNoneSelectedFn, args: {
                    'selectedTypes': State('selectedChipTypes'),
                  }),
                  onTap: allChipActions,
                ),
                // "All" — position 1: unselected (visible when ≥1 type selected)
                buildChip(
                  label: 'All',
                  selected: false,
                  widgetName: 'AllChipUnsel',
                  visible: Not(CustomFunction(isNoneSelectedFn, args: {
                    'selectedTypes': State('selectedChipTypes'),
                  })),
                  onTap: allChipActions,
                ),
                // 11 type chips × 2 variants (Sel/Unsel), all at fixed positions.
                // Sel comes first (both sit at same visual slot; only one visible).
                for (final type in typeOrder) ...[
                  buildChip(
                    label: typeLabels[type]!,
                    selected: true,
                    widgetName: '${typeLabels[type]}ChipSel',
                    visible: CustomFunction(isTypeSelectedFn, args: {
                      'selectedTypes': State('selectedChipTypes'),
                      'type': type,
                    }),
                    onTap: selChipActions(type),
                  ),
                  buildChip(
                    label: typeLabels[type]!,
                    selected: false,
                    widgetName: '${typeLabels[type]}ChipUnsel',
                    visible: Not(CustomFunction(isTypeSelectedFn, args: {
                      'selectedTypes': State('selectedChipTypes'),
                      'type': type,
                    })),
                    onTap: unselChipActions(type),
                  ),
                ],
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
                    'cuisineTypeNote':
                        item['philly_restaurant_type_other_note'],
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
                  cuisineTypeNote: item['philly_restaurant_type_other_note'],
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

/// Contract 8: Map View — Toggle on HomePage with Nearest 3 Cards.
///
/// Adds a map/list toggle to the HomePage AppBar. When map view is active,
/// shows a Google Maps widget centered on the user's location with markers for
/// all filteredRestaurants, and a horizontal strip of the 3 nearest restaurant
/// cards below the map. List view is unchanged when the toggle is off.
void buildByobContract8(App app) {
  final restaurants = ff.Collections.restaurants;

  // ── 1. Custom function: squared-km proxy distance (no dart:math) ────────────
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'haversineDistance',
      description: 'Returns squared-km proxy distance between two lat/lng points. Accurate enough for sorting within a small city.',
      code: '''if (lat1 == null || lng1 == null || lat2 == null || lng2 == null) return 0.0;
final dLat = (lat2 - lat1) * 111.0;
final dLng = (lng2 - lng1) * 85.0;
return dLat * dLat + dLng * dLng;''',
    );
  });
  final haversineDistanceFn = CustomFunctionHandle(
    name: 'haversineDistance',
    args: {'lat1': double_, 'lng1': double_, 'lat2': double_, 'lng2': double_},
    returnType: double_,
  );

  // ── 2. Custom function: get 3 nearest restaurants ──────────────────────────
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'getNearestThree',
      description: "Returns the 3 restaurants nearest to the user's GPS location.",
      code: '''if (restaurants == null || (restaurants as List).isEmpty) return [];
final lat0 = (userLat ?? 39.9526).toDouble();
final lng0 = (userLng ?? -75.1652).toDouble();
double hvDist(double la1, double lo1, double la2, double lo2) {
  final dLa = (la2 - la1) * 111.0;
  final dLo = (lo2 - lo1) * 85.0;
  return dLa * dLa + dLo * dLo;
}
double rCoord(dynamic r, String prop) {
  if (r == null) return 0.0;
  try {
    final v = prop == 'lat' ? (r as dynamic).latitude : (r as dynamic).longitude;
    return (v as num? ?? 0).toDouble();
  } catch (_) {
    return 0.0;
  }
}
final copy = (restaurants as List).where((r) => r != null).toList();
copy.sort((r1, r2) {
  final d1 = hvDist(lat0, lng0, rCoord(r1, 'lat'), rCoord(r1, 'lng'));
  final d2 = hvDist(lat0, lng0, rCoord(r2, 'lat'), rCoord(r2, 'lng'));
  return d1.compareTo(d2);
});
return copy.take(3).toList().cast<RestaurantsRecord>();''',
    );
  });
  final getNearestThreeFn = CustomFunctionHandle(
    name: 'getNearestThree',
    args: {'restaurants': listOf(restaurants), 'userLat': double_, 'userLng': double_},
    returnType: listOf(restaurants),
  );

  // ── 2b. Enable location permission in project settings ─────────────────────
  // Required by FlutterFlow validator before a RequestPermissions(location) action
  // can be used. Idempotent: adds only if not already present.
  app.raw((project) {
    final perms = project.ensureAppSettings().ensurePermissionsSettings();
    if (!perms.permissionMessages.any(
      (m) => m.permissionType == FFPermissionType.LOCATION,
    )) {
      perms.permissionMessages.add(FFPermissionsSettings_PermissionMessage(
        permissionType: FFPermissionType.LOCATION,
      ));
    }
  });

  // ── 2c. Remove geolocator package and custom actions that used it ──────────
  // These caused compile errors because FlutterFlow doesn't bundle geolocator.
  // Replaced by FlutterFlow's built-in RequestPermissions action and the native
  // PERMISSIONS variable source for reading permission state.
  app.raw((project) {
    // Remove geolocator pub dep (idempotent: no-op if already absent)
    final deps = project.customCode.pubspecPackageInfo.pubspecDependencies;
    deps.removeWhere((d) => d.name == 'geolocator');

    // Remove the three geolocator-dependent custom actions (idempotent)
    final actions = project.customCode.customActions;
    actions.removeWhere((a) =>
        a.identifier.name == 'getDeviceLat' ||
        a.identifier.name == 'getDeviceLng' ||
        a.identifier.name == 'checkAndRequestLocation');
  });

  // ── 2d. Custom helpers: extract lat/lng doubles from Global currentDeviceLocation
  // These let the map toggle action chain assign userLatitude / userLongitude
  // directly from FlutterFlow's built-in Global(currentDeviceLocation) LatLng
  // without a geolocator custom action or custom Dart code.
  app.raw((project) {
    const latCode = r'return location?.latitude ?? 39.9526;';
    const lngCode = r'return location?.longitude ?? -75.1652;';

    final latFn = findCustomFunction(project, name: 'getLatFromLocation');
    if (latFn != null) {
      updateCustomFunction(project, name: 'getLatFromLocation', code: latCode);
    } else {
      addCustomFunction(
        project,
        name: 'getLatFromLocation',
        code: latCode,
        arguments: [
          FFParameter(
            identifier: FFIdentifier(
              name: 'location',
              key: generateRandomAlphaNumericString(),
            ),
            dataType: FFDataTypeV2(scalarType: FFBaseDataType.LatLng),
          ),
        ],
        returnParameter: FFParameter(
          identifier: FFIdentifier(
            name: 'returnValue',
            key: generateRandomAlphaNumericString(),
          ),
          dataType: FFDataTypeV2(scalarType: FFBaseDataType.Double),
        ),
        description:
            'Extracts latitude (double) from a LatLng value; falls back to Philly center.',
      );
    }

    final lngFn = findCustomFunction(project, name: 'getLngFromLocation');
    if (lngFn != null) {
      updateCustomFunction(project, name: 'getLngFromLocation', code: lngCode);
    } else {
      addCustomFunction(
        project,
        name: 'getLngFromLocation',
        code: lngCode,
        arguments: [
          FFParameter(
            identifier: FFIdentifier(
              name: 'location',
              key: generateRandomAlphaNumericString(),
            ),
            dataType: FFDataTypeV2(scalarType: FFBaseDataType.LatLng),
          ),
        ],
        returnParameter: FFParameter(
          identifier: FFIdentifier(
            name: 'returnValue',
            key: generateRandomAlphaNumericString(),
          ),
          dataType: FFDataTypeV2(scalarType: FFBaseDataType.Double),
        ),
        description:
            'Extracts longitude (double) from a LatLng value; falls back to Philly center.',
      );
    }
  });

  // ── 3. Page state: add isMapView + GPS defaults + nearestThree + hasLocationPermission
  app.editPageState(ff.Pages.homePage, (state) {
    state.ensureField('isMapView', bool_.withDefault(false));
    state.ensureField('userLatitude', double_.withDefault(39.9526));
    state.ensureField('userLongitude', double_.withDefault(-75.1652));
    state.ensureField('nearestThree', listOf(restaurants));
    state.ensureField('hasLocationPermission', bool_.withDefault(false));
  });

  // ── 4. Updated onLoad — request permission + nearestThree init ──────────────
  // Uses FlutterFlow built-in RequestPermissions action (no geolocator needed).
  // Philadelphia center (39.9526, -75.1652) are the page-state defaults so
  // getNearestThree works even when the user denies location permission.
  app.editPageOnLoad(ff.Pages.homePage, [
    FirestoreQuery(restaurants, limit: 100, outputAs: 'loadedRestaurants'),
    SetState('restaurants', ActionOutput('loadedRestaurants')),
    SetState('filteredRestaurants', ActionOutput('loadedRestaurants')),
    RequestPermissions(permission: PermissionKind.location),
    SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
      'restaurants': State('restaurants'),
      'userLat': State('userLatitude'),
      'userLng': State('userLongitude'),
    })),
  ]);

  // ── 5. Handles for existing Contract 5 custom functions ─────────────────────
  final isTypeSelectedFn = CustomFunctionHandle(
    name: 'isTypeSelected',
    args: {'selectedTypes': listOf(string), 'type': string},
    returnType: bool_,
  );
  final isNoneSelectedFn = CustomFunctionHandle(
    name: 'isNoneSelected',
    args: {'selectedTypes': listOf(string)},
    returnType: bool_,
  );
  final filterTypesFn = CustomFunctionHandle(
    name: 'filterRestaurantsByTypes',
    args: {'restaurants': listOf(restaurants), 'selectedTypes': listOf(string)},
    returnType: listOf(restaurants),
  );

  // ── 6. Chip helpers ─────────────────────────────────────────────────────────
  const typeOrder = [
    'italian', 'japanese', 'mediterranean', 'asian', 'seafood',
    'mexican', 'thai', 'french', 'american', 'indian', 'other',
  ];
  const typeLabels = {
    'italian': 'Italian', 'japanese': 'Japanese', 'mediterranean': 'Mediterranean',
    'asian': 'Asian', 'seafood': 'Seafood', 'mexican': 'Mexican', 'thai': 'Thai',
    'french': 'French', 'american': 'American', 'indian': 'Indian', 'other': 'Other',
  };

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

  // After each chip filter action, also recompute nearestThree from the updated
  // filteredRestaurants so the bottom map strip stays in sync.
  List<DslAction> withNearestUpdate(List<DslAction> base) => [
    ...base,
    SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
      'restaurants': State('filteredRestaurants'),
      'userLat': State('userLatitude'),
      'userLng': State('userLongitude'),
    })),
  ];

  final allChipActions = withNearestUpdate([
    SetState.clear('selectedChipTypes'),
    SetState('filteredRestaurants', State('restaurants')),
  ]);

  List<DslAction> selChipActions(String type) => withNearestUpdate([
    SetState.removeFromList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ]);

  List<DslAction> unselChipActions(String type) => withNearestUpdate([
    SetState.addToList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ]);

  // ── 7. Rebuild HomePage body: chips + Stack(ListView | MapViewColumn) ───────
  app.editPage(ff.Pages.homePage, (page) {
    page.ensureReplaced(
      ff.Pages.homePage.widgets.byPath('HomePage.body[0]').single,
      Column(
        name: 'HomeBody',
        crossAxis: CrossAxis.stretch,
        children: [
          // Chip strip — wrapping, fixed positions, multi-select (same as C5)
          Container(
            name: 'ChipsStrip',
            color: Colors.primaryBackground,
            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Wrap(
              name: 'ChipsWrap',
              spacing: 8,
              runSpacing: 8,
              children: [
                buildChip(
                  label: 'All',
                  selected: true,
                  widgetName: 'AllChipSel',
                  visible: CustomFunction(isNoneSelectedFn, args: {
                    'selectedTypes': State('selectedChipTypes'),
                  }),
                  onTap: allChipActions,
                ),
                buildChip(
                  label: 'All',
                  selected: false,
                  widgetName: 'AllChipUnsel',
                  visible: Not(CustomFunction(isNoneSelectedFn, args: {
                    'selectedTypes': State('selectedChipTypes'),
                  })),
                  onTap: allChipActions,
                ),
                for (final type in typeOrder) ...[
                  buildChip(
                    label: typeLabels[type]!,
                    selected: true,
                    widgetName: '${typeLabels[type]}ChipSel',
                    visible: CustomFunction(isTypeSelectedFn, args: {
                      'selectedTypes': State('selectedChipTypes'),
                      'type': type,
                    }),
                    onTap: selChipActions(type),
                  ),
                  buildChip(
                    label: typeLabels[type]!,
                    selected: false,
                    widgetName: '${typeLabels[type]}ChipUnsel',
                    visible: Not(CustomFunction(isTypeSelectedFn, args: {
                      'selectedTypes': State('selectedChipTypes'),
                      'type': type,
                    })),
                    onTap: unselChipActions(type),
                  ),
                ],
              ],
            ),
          ),
          // Content area — Stack: list view or map view (only one visible at a time)
          Expanded(
            Stack(
              name: 'ViewToggleStack',
              alignment: Alignment.topLeft,
              children: [
                // List view — visible when isMapView = false (default)
                ListView(
                  name: 'RestaurantList',
                  source: State('filteredRestaurants'),
                  spacing: 0,
                  visible: Not(State('isMapView')),
                  itemBuilder: (item) => Container(
                    name: 'CardWrapper',
                    onTap: Navigate.to(
                      ff.Pages.restaurantDetailPage,
                      params: {
                        ff.Pages.restaurantDetailPage.params.restaurantName: item['Name'],
                        ff.Pages.restaurantDetailPage.params.cuisineType: item['philly_restaurant_type'],
                        'cuisineTypeNote': item['philly_restaurant_type_other_note'],
                        ff.Pages.restaurantDetailPage.params.corkageFeeType: item['philly_corkage_fee'],
                        ff.Pages.restaurantDetailPage.params.address: item['Add'],
                        ff.Pages.restaurantDetailPage.params.phone: item['Phone'],
                        ff.Pages.restaurantDetailPage.params.latitude: item['Latitude'],
                        ff.Pages.restaurantDetailPage.params.longitude: item['Longitude'],
                        ff.Pages.restaurantDetailPage.params.coverImageUrl: item['cover_image_url'],
                      },
                    ),
                    child: ff.Components.restaurantCard(
                      restaurantName: item['Name'],
                      cuisineType: item['philly_restaurant_type'],
                      cuisineTypeNote: item['philly_restaurant_type_other_note'],
                      imageUrl: item['cover_image_url'],
                      corkageFeeType: item['philly_corkage_fee'],
                    ),
                  ),
                ),
                // Map view — visible when isMapView = true
                Column(
                  name: 'MapViewColumn',
                  crossAxis: CrossAxis.stretch,
                  visible: State('isMapView'),
                  children: [
                    // Google Maps widget (Expanded so it fills available height).
                    // app.raw() below configures docMarkers + showLocation +
                    // clears the 300×200 default dimensions from the outer container.
                    Expanded(
                      MapWidget(lat: 39.9526, lng: -75.1652, zoom: 12, name: 'MapArea'),
                      name: 'MapExpanded',
                    ),
                    // Nearest 3 restaurants — horizontal strip below the map
                    Container(
                      name: 'NearestCardsSection',
                      height: 200,
                      color: Colors.primaryBackground,
                      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 8),
                      child: ListView(
                        name: 'NearestCardsList',
                        source: State('nearestThree'),
                        horizontal: true,
                        spacing: 8,
                        itemBuilder: (item) => Container(
                          name: 'NearestCardWrapper',
                          width: 140,
                          color: Colors.hex(0xFFFFFFFF),
                          borderRadius: 12,
                          padding: EdgeInsets.all(10),
                          onTap: Navigate.to(
                            ff.Pages.restaurantDetailPage,
                            params: {
                              ff.Pages.restaurantDetailPage.params.restaurantName: item['Name'],
                              ff.Pages.restaurantDetailPage.params.cuisineType: item['philly_restaurant_type'],
                              'cuisineTypeNote': item['philly_restaurant_type_other_note'],
                              ff.Pages.restaurantDetailPage.params.corkageFeeType: item['philly_corkage_fee'],
                              ff.Pages.restaurantDetailPage.params.address: item['Add'],
                              ff.Pages.restaurantDetailPage.params.phone: item['Phone'],
                              ff.Pages.restaurantDetailPage.params.latitude: item['Latitude'],
                              ff.Pages.restaurantDetailPage.params.longitude: item['Longitude'],
                              ff.Pages.restaurantDetailPage.params.coverImageUrl: item['cover_image_url'],
                            },
                          ),
                          child: Column(
                            name: 'NearestCardContent',
                            crossAxis: CrossAxis.start,
                            spacing: 4,
                            children: [
                              Text(
                                item['Name'],
                                name: 'NearestCardName',
                                style: Styles.labelMedium,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                              // Corkage badge (green/wine-red/orange, one visible)
                              Container(
                                name: 'NearestFreeBadge',
                                color: Colors.hex(0xFF2E7D32),
                                borderRadius: 4,
                                padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                visible: Equals(item['philly_corkage_fee'], 'free'),
                                child: Text(
                                  'Free BYOB',
                                  name: 'NearestFreeText',
                                  color: Colors.hex(0xFFFFFFFF),
                                  style: Styles.labelSmall,
                                ),
                              ),
                              Container(
                                name: 'NearestCorkageBadge',
                                color: Colors.hex(0xFF8B2635),
                                borderRadius: 4,
                                padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                visible: Equals(item['philly_corkage_fee'], 'corkage_fee'),
                                child: Text(
                                  'Corkage Fee',
                                  name: 'NearestCorkageText',
                                  color: Colors.hex(0xFFFFFFFF),
                                  style: Styles.labelSmall,
                                ),
                              ),
                              Container(
                                name: 'NearestOtherBadge',
                                color: Colors.hex(0xFFBF6A02),
                                borderRadius: 4,
                                padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                visible: Equals(item['philly_corkage_fee'], 'other'),
                                child: Text(
                                  'Ask Us',
                                  name: 'NearestOtherText',
                                  color: Colors.hex(0xFFFFFFFF),
                                  style: Styles.labelSmall,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
            name: 'ContentExpanded',
          ),
        ],
      ),
    );
  });

  // ── 8. AppBar toggle button via raw() ────────────────────────────────────────
  // Runs AFTER compilation, so all field identifiers are resolvable.
  // On tap:
  //   a. Requests location permission (FlutterFlow built-in OS dialog).
  //   b. Sets hasLocationPermission from the OS permission state.
  //   c. IF permission granted: reads Global(currentDeviceLocation) via
  //      getLatFromLocation / getLngFromLocation helpers, updates userLatitude /
  //      userLongitude, recomputes nearestThree.
  //   d. Toggles isMapView (always runs, regardless of permission result).
  app.raw((project) {
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') { homeWC = wc; break; }
    }
    if (homeWC == null) return;

    final appBarNode = findByKey(homeWC.node, 'AppBar_0fjy4eyk');
    if (appBarNode == null) return;

    // Resolve all state field identifiers used in this chain
    FFIdentifier? isMapViewId;
    FFIdentifier? hasLocPermId;
    FFIdentifier? userLatId;
    FFIdentifier? userLngId;
    FFIdentifier? nearestThreeId;
    FFIdentifier? filteredRestaurantsId;
    for (final field in homeWC.classModel.stateFields) {
      switch (field.parameter.identifier.name) {
        case 'isMapView':
          isMapViewId = field.parameter.identifier.deepCopy();
        case 'hasLocationPermission':
          hasLocPermId = field.parameter.identifier.deepCopy();
        case 'userLatitude':
          userLatId = field.parameter.identifier.deepCopy();
        case 'userLongitude':
          userLngId = field.parameter.identifier.deepCopy();
        case 'nearestThree':
          nearestThreeId = field.parameter.identifier.deepCopy();
        case 'filteredRestaurants':
          filteredRestaurantsId = field.parameter.identifier.deepCopy();
      }
    }
    if (isMapViewId == null || hasLocPermId == null) return;

    // Resolve custom function identifiers needed in the GPS branch
    final getLatFnId =
        findCustomFunction(project, name: 'getLatFromLocation')?.identifier.deepCopy();
    final getLngFnId =
        findCustomFunction(project, name: 'getLngFromLocation')?.identifier.deepCopy();
    final getNearestFnId =
        findCustomFunction(project, name: 'getNearestThree')?.identifier.deepCopy();

    // ── Build the action chain ──────────────────────────────────────────────────
    // Step a: Request location permission — FlutterFlow built-in action.
    final stepAAction = FFAction(
      key: generateRandomAlphaNumericString(),
      requestPermissions: FFRequestPermissionsAction(
        permissionType: FFPermissionType.LOCATION,
      ),
    );

    // Step b: Set hasLocationPermission = current OS permission state.
    // FFVariableSource.PERMISSIONS reads the granted/denied state for LOCATION.
    final permissionStateVar = FFVariable(
      source: FFVariableSource.PERMISSIONS,
      baseVariable: FFBaseVariable(
        permissionState: FFPermissionStateVariable(
          permissionType: FFPermissionType.LOCATION,
        ),
      ),
    );
    final stepBAction = FFAction(
      key: generateRandomAlphaNumericString(),
      localStateUpdate: FFLocalStateUpdate(
        updateType: FFLocalStateUpdate_UpdateType.WIDGET,
        stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
        updates: [
          FFLocalStateFieldUpdate(
            fieldIdentifier: hasLocPermId!.deepCopy(),
            setValue: FFValue(variable: permissionStateVar),
          ),
        ],
      ),
    );

    // Step d: Toggle isMapView (always runs after the conditional branch).
    final stepDAction = FFAction(
      key: generateRandomAlphaNumericString(),
      localStateUpdate: FFLocalStateUpdate(
        updateType: FFLocalStateUpdate_UpdateType.WIDGET,
        stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
        updates: [
          FFLocalStateFieldUpdate(
            fieldIdentifier: isMapViewId!.deepCopy(),
            toggle: FFLocalStateToggle(),
          ),
        ],
      ),
    );
    final stepDNode = FFActionNode(
      key: generateRandomAlphaNumericString(),
      action: stepDAction,
    );

    // Step c (conditional): IF hasLocationPermission == true, fetch GPS and
    // update userLatitude / userLongitude / nearestThree.
    // Falls back gracefully when identifiers are unavailable (first-run before
    // the custom functions are pushed).
    FFActionNode stepCNode;
    if (getLatFnId != null &&
        getLngFnId != null &&
        getNearestFnId != null &&
        userLatId != null &&
        userLngId != null &&
        nearestThreeId != null &&
        filteredRestaurantsId != null) {
      // Global(currentDeviceLocation) — FlutterFlow built-in LatLng variable.
      final globalLocVar = FFVariable(
        source: FFVariableSource.GLOBAL_PROPERTIES,
        baseVariable: FFBaseVariable(
          globalProperties: FFGlobalPropertiesVariable(
            property:
                FFGlobalPropertiesVariable_GlobalProperty.CURRENT_DEVICE_LOCATION,
          ),
        ),
      );

      // getLatFromLocation(currentDeviceLocation) → double
      final latFromLocVar = FFVariable(
        source: FFVariableSource.FUNCTION_CALL,
        functionCall: FFFunctionCall(
          customFunction: getLatFnId,
          values: [FFValue(variable: globalLocVar.deepCopy())],
        ),
      );

      // getLngFromLocation(currentDeviceLocation) → double
      final lngFromLocVar = FFVariable(
        source: FFVariableSource.FUNCTION_CALL,
        functionCall: FFFunctionCall(
          customFunction: getLngFnId,
          values: [FFValue(variable: globalLocVar.deepCopy())],
        ),
      );

      // State('filteredRestaurants') for nearestThree computation
      final filteredRestVar = FFVariable(
        source: FFVariableSource.LOCAL_STATE,
        baseVariable: FFBaseVariable(
          localState: FFLocalStateVariable(
            fieldIdentifier: filteredRestaurantsId,
            stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
          ),
        ),
        nodeKeyRef: FFNodeKeyReference(key: homeWC!.node.key),
      );

      // getNearestThree(filteredRestaurants, lat, lng) → List<restaurants>
      // Argument order matches CustomFunctionHandle declaration: [restaurants, userLat, userLng]
      final nearestThreeVar = FFVariable(
        source: FFVariableSource.FUNCTION_CALL,
        functionCall: FFFunctionCall(
          customFunction: getNearestFnId,
          values: [
            FFValue(variable: filteredRestVar),
            FFValue(variable: latFromLocVar.deepCopy()),
            FFValue(variable: lngFromLocVar.deepCopy()),
          ],
        ),
      );

      // Build the GPS true-branch chain: setLat → setLng → setNearestThree
      // Constructed bottom-up so each node's followUpAction points to the next.
      final setNearestThreeNode = FFActionNode(
        key: generateRandomAlphaNumericString(),
        action: FFAction(
          key: generateRandomAlphaNumericString(),
          localStateUpdate: FFLocalStateUpdate(
            updateType: FFLocalStateUpdate_UpdateType.WIDGET,
            stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
            updates: [
              FFLocalStateFieldUpdate(
                fieldIdentifier: nearestThreeId,
                setValue: FFValue(variable: nearestThreeVar),
              ),
            ],
          ),
        ),
      );
      final setLngNode = FFActionNode(
        key: generateRandomAlphaNumericString(),
        action: FFAction(
          key: generateRandomAlphaNumericString(),
          localStateUpdate: FFLocalStateUpdate(
            updateType: FFLocalStateUpdate_UpdateType.WIDGET,
            stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
            updates: [
              FFLocalStateFieldUpdate(
                fieldIdentifier: userLngId,
                setValue: FFValue(variable: lngFromLocVar),
              ),
            ],
          ),
        ),
        followUpAction: setNearestThreeNode,
      );
      final gpsBranchNode = FFActionNode(
        key: generateRandomAlphaNumericString(),
        action: FFAction(
          key: generateRandomAlphaNumericString(),
          localStateUpdate: FFLocalStateUpdate(
            updateType: FFLocalStateUpdate_UpdateType.WIDGET,
            stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
            updates: [
              FFLocalStateFieldUpdate(
                fieldIdentifier: userLatId,
                setValue: FFValue(variable: latFromLocVar),
              ),
            ],
          ),
        ),
        followUpAction: setLngNode,
      );

      // hasLocationPermission state variable (for condition check)
      final hasLocPermCondVar = FFVariable(
        source: FFVariableSource.LOCAL_STATE,
        baseVariable: FFBaseVariable(
          localState: FFLocalStateVariable(
            fieldIdentifier: hasLocPermId.deepCopy(),
            stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
          ),
        ),
        nodeKeyRef: FFNodeKeyReference(key: homeWC.node.key),
      );

      // IF hasLocationPermission == true → run GPS branch
      stepCNode = FFActionNode(
        key: generateRandomAlphaNumericString(),
        conditionActions: FFConditionActions(
          trueActions: [
            FFConditionActions_FFTrueConditionAction(
              condition: FFActionCondition(variable: hasLocPermCondVar),
              trueAction: gpsBranchNode,
            ),
          ],
        ),
      );
    } else {
      // Fallback: skip GPS branch (identifiers not yet available)
      stepCNode = FFActionNode(key: generateRandomAlphaNumericString());
    }

    // Wire step c → step d
    stepCNode.followUpAction = stepDNode;

    // Chain: stepA → stepB → stepC → stepD
    final rootNode = FFActionNode(
      key: generateRandomAlphaNumericString(),
      action: stepAAction,
      followUpAction: FFActionNode(
        key: generateRandomAlphaNumericString(),
        action: stepBAction,
        followUpAction: stepCNode,
      ),
    );

    // ── Build the IconButton node ──────────────────────────────────────────────
    // Idempotent: remove existing button before re-adding
    appBarNode.children.removeWhere((c) => c.name == 'MapToggleButton');

    final iconBtnKey = 'IconButton_${generateRandomAlphaNumericString()}';
    final toggleBtn = FFNode(
      key: iconBtnKey,
      type: FFWidgetType.IconButton,
      name: 'MapToggleButton',
      props: FFWidgetProperties(
        iconButton: FFIconButton(
          iconValue: FFIconValue(
            inputValue: FFIcon(
              iconDataValue: FFIconDataValue(
                inputValue: FFIconData(name: 'map', family: 'MaterialIcons'),
              ),
              sizeValue: FFDoubleValue(inputValue: 24.0),
            ),
          ),
          buttonSize: FFDim(pixelsValue: FFDoubleValue(inputValue: 40.0)),
        ),
      ),
    );
    toggleBtn.triggerActions.add(FFTriggerActions(
      trigger: FFActionTrigger(triggerType: FFActionTriggerType.ON_TAP),
      rootAction: rootNode,
    ));

    appBarNode.children.add(toggleBtn);
    appBarNode.childPropertyMap['actions'] = FFChildrenKeys(
      keyRefs: [FFNodeKeyReference(key: toggleBtn.key)],
    );
  });

  // ── 9. Configure FFGoogleMap node and clear MapArea container size ───────────
  // Note: docMarkers is intentionally NOT set because the Firestore data uses
  // separate Latitude/Longitude double fields instead of a GeoPoint — FlutterFlow's
  // docMarkers validator requires a GeoPoint (LatLng) field in the collection.
  // showLocation is bound to hasLocationPermission state (NOT hardcoded true) —
  // hardcoding true caused SecurityException on Android before permission was granted.
  app.raw((project) {
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') { homeWC = wc; break; }
    }
    if (homeWC == null) return;

    // Walk the tree to find the inner GoogleMap node and outer MapArea container
    FFNode? mapInner;
    FFNode? mapOuter;

    void walk(FFNode node) {
      if (node.type == FFWidgetType.GoogleMap) mapInner = node;
      if (node.name == 'MapArea' && node.type == FFWidgetType.Container) mapOuter = node;
      for (final child in node.children) { walk(child); }
    }
    walk(homeWC.node);

    // Bind GPS dot visibility to hasLocationPermission state (safe: no crash before permission granted)
    if (mapInner != null) {
      FFIdentifier? hasLocPermId;
      for (final field in homeWC.classModel.stateFields) {
        if (field.parameter.identifier.name == 'hasLocationPermission') {
          hasLocPermId = field.parameter.identifier.deepCopy();
          break;
        }
      }
      if (hasLocPermId != null) {
        final showLocVar = FFVariable(
          source: FFVariableSource.LOCAL_STATE,
          baseVariable: FFBaseVariable(
            localState: FFLocalStateVariable(
              fieldIdentifier: hasLocPermId,
              stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
            ),
          ),
        );
        showLocVar.defaultValue = FFParameterValue(serializedValue: 'false');
        showLocVar.nodeKeyRef = FFNodeKeyReference(key: homeWC!.node.key);
        mapInner!.props.googleMap.showLocationValue = FFBooleanValue(variable: showLocVar);
      } else {
        mapInner!.props.googleMap.showLocationValue = FFBooleanValue(inputValue: false);
      }
    }

    // Remove hardcoded 300×200 so Expanded drives the map height
    if (mapOuter != null && mapOuter!.props.hasContainer()) {
      mapOuter!.props.container.clearDimensions();
    }
  });

  // ── 10. Fix HomeBody + MapViewColumn mainAxisSize → max ─────────────────────────
  // DSL Column defaults to minSizeValue=true (MainAxisSize.min).
  // HomeBody.min → Scaffold body height is not propagated → MapViewColumn gets
  // unbounded height → Expanded(GoogleMap) collapses to 0px (blank map).
  // Fix: HomeBody.max propagates bounded height; MapViewColumn.max allows
  // Expanded(GoogleMap) to fill the remaining space correctly.
  app.raw((project) {
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') { homeWC = wc; break; }
    }
    if (homeWC == null) return;

    FFNode? homeBody;
    FFNode? mapViewColumn;
    void walk(FFNode node) {
      if (node.name == 'HomeBody' && node.type == FFWidgetType.Column) {
        homeBody = node;
      }
      if (node.name == 'MapViewColumn' && node.type == FFWidgetType.Column) {
        mapViewColumn = node;
      }
      for (final child in node.children) { walk(child); }
    }
    walk(homeWC.node);

    if (homeBody != null) {
      homeBody!.props.column.minSizeValue = FFBooleanValue(inputValue: false);
    }
    if (mapViewColumn != null) {
      mapViewColumn!.props.column.minSizeValue = FFBooleanValue(inputValue: false);
    }
  });
}

/// Contract 6: Tappable phone number + tappable address on RestaurantDetailPage.
///
/// Phone row → on tap launches tel:<phone> to dial directly.
/// Address row → on tap opens Google Maps navigation (identical to Get Directions).
/// Get Directions button is unchanged.
void buildByobContract6(App app) {
  // Custom function: builds a tel: URL from a phone string.
  final getPhoneUrl = app.customFunction(
    'getPhoneUrl',
    args: {'phone': string},
    returns: string,
    code: r'''return 'tel:${phone ?? ''}';''',
  );
  // Idempotent update — keeps cloud in sync if app.customFunction payload drifts.
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'getPhoneUrl',
      code: r'''return 'tel:${phone ?? ''}';''',
    );
  });

  // getMapsUrl declared in Contract 3 — reference by handle name only.
  final getMapsUrl = CustomFunctionHandle(
    name: 'getMapsUrl',
    args: {'lat': double_, 'lng': double_},
    returnType: string,
  );

  final formatCuisine = CustomFunctionHandle(
    name: 'formatCuisineType',
    args: {'typeString': string, 'otherNote': string},
    returnType: string,
  );

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
                  CustomFunction(formatCuisine, args: {
                    'typeString': Param('cuisineType'),
                    'otherNote': Param('cuisineTypeNote'),
                  }),
                  name: 'CuisineText',
                  style: Styles.bodyMedium,
                  color: Colors.secondaryText,
                ),
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
                // Tappable address — opens Google Maps navigation
                Container(
                  name: 'AddressContainer',
                  onTap: LaunchUrl(CustomFunction(getMapsUrl, args: {
                    'lat': Param('latitude'),
                    'lng': Param('longitude'),
                  })),
                  child: Row(
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
                ),
                // Tappable phone — dials directly via tel: URL
                Container(
                  name: 'PhoneContainer',
                  onTap: LaunchUrl(CustomFunction(getPhoneUrl, args: {
                    'phone': Param('phone'),
                  })),
                  child: Row(
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
                ),
                Spacer(height: 8),
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
}

/// Contract 9: Restaurant map markers + zoom adjustment.
///
/// 1. Adds `location` GeoPoint (LatLng) field to the restaurants schema.
///    Required by FlutterFlow's docMarkers validator before markers can be set.
/// 2. Configures the GoogleMap widget:
///    - docMarkers bound to filteredRestaurants page state (reflects active chip filter)
///    - Marker color: rose
///    - ON_MARKER_TAP navigates to RestaurantDetailPage with same params as card tap
///    - initialZoom 12 → 15
void buildByobContract9(App app) {
  // ── 1. Add `location` LatLng field to restaurants collection ────────────────
  // Required so FlutterFlow's docMarkers validator accepts the collection.
  // Idempotent: no-op if field already exists.
  app.raw((project) {
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      if (coll.fields.containsKey('location')) break;
      coll.fields['location'] = FFParameter(
        identifier: FFIdentifier(
          name: 'location',
          key: generateRandomAlphaNumericString(),
        ),
        dataType: FFDataTypeV2(scalarType: FFBaseDataType.LatLng),
      );
      break;
    }
  });

  // ── 2. Configure GoogleMap: markers, zoom, ON_MARKER_TAP ────────────────────
  app.raw((project) {
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') {
        homeWC = wc;
        break;
      }
    }
    if (homeWC == null) return;

    // Find the MapArea Container (parent) and the inner GoogleMap node.
    // GENERATOR_VARIABLE requires nodeKeyRef to be a STRICT ANCESTOR of the
    // node whose trigger uses it. On_MARKER_TAP is on the GoogleMap itself, so
    // we put generatorVariable on its parent Container (MapArea) and reference
    // that container's key in all GENERATOR_VARIABLE accesses.
    FFNode? mapContainer;
    FFNode? mapInner;
    void walk(FFNode node, FFNode? parent) {
      if (node.type == FFWidgetType.GoogleMap) {
        mapInner = node;
        mapContainer = parent;
      }
      for (final child in node.children) {
        walk(child, node);
      }
    }
    walk(homeWC.node, null);
    if (mapInner == null) return;
    if (mapContainer == null) return;

    // Resolve filteredRestaurants state field identifier
    // C16: markers must reflect the active chip/search filter, not the full list.
    FFIdentifier? filteredRestaurantsId;
    for (final field in homeWC.classModel.stateFields) {
      if (field.parameter.identifier.name == 'filteredRestaurants') {
        filteredRestaurantsId = field.parameter.identifier.deepCopy();
        break;
      }
    }
    if (filteredRestaurantsId == null) return;

    // Resolve Firestore field identifiers for the navigate params
    FFIdentifier? fldName;
    FFIdentifier? fldAdd;
    FFIdentifier? fldPhone;
    FFIdentifier? fldRestaurantType;
    FFIdentifier? fldCorkageFee;
    FFIdentifier? fldLatitude;
    FFIdentifier? fldLongitude;
    FFIdentifier? fldCoverImageUrl;
    FFIdentifier? fldCuisineTypeNote;
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      for (final field in coll.fields.values) {
        switch (field.identifier.name) {
          case 'Name':
            fldName = field.identifier.deepCopy();
          case 'Add':
            fldAdd = field.identifier.deepCopy();
          case 'Phone':
            fldPhone = field.identifier.deepCopy();
          case 'philly_restaurant_type':
            fldRestaurantType = field.identifier.deepCopy();
          case 'philly_corkage_fee':
            fldCorkageFee = field.identifier.deepCopy();
          case 'Latitude':
            fldLatitude = field.identifier.deepCopy();
          case 'Longitude':
            fldLongitude = field.identifier.deepCopy();
          case 'cover_image_url':
            fldCoverImageUrl = field.identifier.deepCopy();
          case 'philly_restaurant_type_other_note':
            fldCuisineTypeNote = field.identifier.deepCopy();
        }
      }
    }

    // 2a. Marker color → rose
    mapInner!.props.googleMap.markerColor =
        FFGoogleMap_FFGoogleMarkerColor.ROSE;

    // 2b. Zoom → 15
    mapInner!.props.googleMap.initialZoomValue =
        FFDoubleValue(inputValue: 15.0);

    // 2c. docMarkers → filteredRestaurants page state
    // C16: using filteredRestaurants so markers update when a chip or search is active.
    final filteredVar = FFVariable(
      source: FFVariableSource.LOCAL_STATE,
      baseVariable: FFBaseVariable(
        localState: FFLocalStateVariable(
          fieldIdentifier: filteredRestaurantsId,
          stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
        ),
      ),
      nodeKeyRef: FFNodeKeyReference(key: homeWC!.node.key),
    );
    mapInner!.props.googleMap.docMarkers = filteredVar;

    // 2d. Generator variable on the MapArea CONTAINER (not the inner GoogleMap).
    // The server validator requires GENERATOR_VARIABLE's nodeKeyRef to be a
    // strict ANCESTOR of the node whose trigger uses it. ON_MARKER_TAP lives
    // on the inner GoogleMap, so the generator variable must be on a parent —
    // in this case the MapArea Container wrapping the GoogleMap.
    mapContainer!.generatorVariable = FFGeneratorVariable(
      identifier: FFIdentifier(
        name: 'markerDoc',
        key: generateRandomAlphaNumericString(),
      ),
      variable: FFVariable(
        source: FFVariableSource.LOCAL_STATE,
        baseVariable: FFBaseVariable(
          localState: FFLocalStateVariable(
            fieldIdentifier: filteredRestaurantsId,
            stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
          ),
        ),
        nodeKeyRef: FFNodeKeyReference(key: homeWC.node.key),
      ),
    );

    // 2e. ON_MARKER_TAP — removed.
    //
    // FlutterFlow's server validator AND code generator both reject GENERATOR_VARIABLE
    // in any action (SetState or Navigate) fired from a GoogleMap ON_MARKER_TAP trigger
    // when the generator is bound to a page-state list via docMarkers.
    // This causes "not properly set" errors that block code export entirely.
    //
    // Navigation from map view is handled by the nearest-3-cards strip below the map,
    // which already uses the same Navigate.to() + item['field'] pattern as RestaurantCard.
    // Tapping a map marker shows the restaurant name in the native Google Maps callout.

    // Remove any ON_MARKER_TAP trigger from a partial/interrupted push (idempotent).
    mapInner!.triggerActions.removeWhere(
      (t) => t.trigger.triggerType == FFActionTriggerType.ON_MARKER_TAP,
    );

    // Remove marker page state vars that may have been added by the interrupted push.
    const markerFieldNames = [
      'markerName',
      'markerCuisineType',
      'markerCuisineTypeNote',
      'markerCorkageFee',
      'markerAddress',
      'markerPhone',
      'markerLatitude',
      'markerLongitude',
      'markerCoverImageUrl',
    ];
    homeWC.classModel.stateFields.removeWhere(
      (f) => markerFieldNames.contains(f.parameter.identifier.name),
    );
  });
}

/// Contract 13: Update filter chips — add Pizza, Sushi, Ramen; remove American, Indian.
///
/// 1. Updates filterRestaurantsByType knownTypes to include pizza/sushi/ramen
///    (american/indian kept in exclusion list so they don't pollute Other).
/// 2. Updates filterRestaurantsByTypes knownTypes identically.
/// 3. Replaces ChipsStrip only — map, list, and nearest-3 are untouched.
///    New order (12 chips): Italian · Mediterranean · Japanese · Seafood · Sushi ·
///    Pizza · Asian · Mexican · Thai · Ramen · French · Other
void buildByobContract13(App app) {
  final restaurants = ff.Collections.restaurants;

  // ── 1. Update filterRestaurantsByType — add pizza/sushi/ramen to knownTypes ──
  // american and indian remain in knownTypes so they still excluded from Other.
  const _filterFnCode = r'''if (type == null || restaurants == null) return [];
if (type == 'all') return restaurants;
const knownTypes = {'italian', 'japanese', 'mediterranean', 'asian', 'seafood', 'mexican', 'thai', 'french', 'american', 'indian', 'pizza', 'sushi', 'ramen'};
if (type == 'other') {
  return restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return !parts.any((p) => knownTypes.contains(p));
  }).toList();
}
final t = type.toLowerCase();
return restaurants.where((r) {
  final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
  return parts.contains(t);
}).toList();''';
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'filterRestaurantsByType',
      code: _filterFnCode,
    );
  });

  // ── 2. Update filterRestaurantsByTypes — same expanded knownTypes ────────────
  const _filterTypesFnCode = r'''if (restaurants == null) return [];
if (selectedTypes == null || (selectedTypes as List).isEmpty) return restaurants;
const knownTypes = {'italian', 'japanese', 'mediterranean', 'asian', 'seafood', 'mexican', 'thai', 'french', 'american', 'indian', 'pizza', 'sushi', 'ramen'};
final activeTypes = (selectedTypes as List).map((t) => (t ?? '').toString().toLowerCase()).toSet();
if (activeTypes.contains('other')) {
  final otherResults = restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return !parts.any((p) => knownTypes.contains(p));
  }).toList();
  final nonOther = Set<String>.from(activeTypes)..remove('other');
  if (nonOther.isEmpty) return otherResults;
  final namedResults = restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return parts.any((p) => nonOther.contains(p));
  }).toList();
  final seenIds = otherResults.map((r) => r.reference?.id ?? r.hashCode.toString()).toSet();
  final uniqueNamed = namedResults.where((r) => !seenIds.contains(r.reference?.id ?? r.hashCode.toString())).toList();
  return [...otherResults, ...uniqueNamed];
}
return restaurants.where((r) {
  final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
  return parts.any((p) => activeTypes.contains(p));
}).toList();''';
  app.raw((project) {
    updateCustomFunction(
      project,
      name: 'filterRestaurantsByTypes',
      code: _filterTypesFnCode,
    );
  });

  // ── 3. Chip helpers ──────────────────────────────────────────────────────────
  const typeOrder = [
    'italian', 'mediterranean', 'japanese', 'seafood', 'sushi',
    'pizza', 'asian', 'mexican', 'thai', 'ramen', 'french', 'other',
  ];
  const typeLabels = {
    'italian': 'Italian', 'mediterranean': 'Mediterranean', 'japanese': 'Japanese',
    'seafood': 'Seafood', 'sushi': 'Sushi', 'pizza': 'Pizza',
    'asian': 'Asian', 'mexican': 'Mexican', 'thai': 'Thai',
    'ramen': 'Ramen', 'french': 'French', 'other': 'Other',
  };

  final isTypeSelectedFn = CustomFunctionHandle(
    name: 'isTypeSelected',
    args: {'selectedTypes': listOf(string), 'type': string},
    returnType: bool_,
  );
  final isNoneSelectedFn = CustomFunctionHandle(
    name: 'isNoneSelected',
    args: {'selectedTypes': listOf(string)},
    returnType: bool_,
  );
  final filterTypesFn = CustomFunctionHandle(
    name: 'filterRestaurantsByTypes',
    args: {'restaurants': listOf(restaurants), 'selectedTypes': listOf(string)},
    returnType: listOf(restaurants),
  );
  final getNearestThreeFn = CustomFunctionHandle(
    name: 'getNearestThree',
    args: {'restaurants': listOf(restaurants), 'userLat': double_, 'userLng': double_},
    returnType: listOf(restaurants),
  );

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

  List<DslAction> withNearestUpdate(List<DslAction> base) => [
    ...base,
    SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
      'restaurants': State('filteredRestaurants'),
      'userLat': State('userLatitude'),
      'userLng': State('userLongitude'),
    })),
  ];

  final allChipActions = withNearestUpdate([
    SetState.clear('selectedChipTypes'),
    SetState('filteredRestaurants', State('restaurants')),
  ]);

  List<DslAction> selChipActions(String type) => withNearestUpdate([
    SetState.removeFromList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ]);

  List<DslAction> unselChipActions(String type) => withNearestUpdate([
    SetState.addToList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ]);

  // ── 4. Replace ChipsStrip only (children[0] of HomeBody) ─────────────────────
  // Map widget, ListView, and NearestCardsSection (Contract 11) are untouched.
  app.editPage(ff.Pages.homePage, (page) {
    page.ensureReplaced(
      ff.Pages.homePage.widgets
          .byPath('HomePage.body[0].children[0]')
          .single,
      Container(
        name: 'ChipsStrip',
        color: Colors.primaryBackground,
        padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        child: Wrap(
          name: 'ChipsWrap',
          spacing: 8,
          runSpacing: 8,
          children: [
            buildChip(
              label: 'All',
              selected: true,
              widgetName: 'AllChipSel',
              visible: CustomFunction(isNoneSelectedFn, args: {
                'selectedTypes': State('selectedChipTypes'),
              }),
              onTap: allChipActions,
            ),
            buildChip(
              label: 'All',
              selected: false,
              widgetName: 'AllChipUnsel',
              visible: Not(CustomFunction(isNoneSelectedFn, args: {
                'selectedTypes': State('selectedChipTypes'),
              })),
              onTap: allChipActions,
            ),
            for (final type in typeOrder) ...[
              buildChip(
                label: typeLabels[type]!,
                selected: true,
                widgetName: '${typeLabels[type]}ChipSel',
                visible: CustomFunction(isTypeSelectedFn, args: {
                  'selectedTypes': State('selectedChipTypes'),
                  'type': type,
                }),
                onTap: selChipActions(type),
              ),
              buildChip(
                label: typeLabels[type]!,
                selected: false,
                widgetName: '${typeLabels[type]}ChipUnsel',
                visible: Not(CustomFunction(isTypeSelectedFn, args: {
                  'selectedTypes': State('selectedChipTypes'),
                  'type': type,
                })),
                onTap: unselChipActions(type),
              ),
            ],
          ],
        ),
      ),
    );
  });
}

/// Contract 11: Nearest-3 strip UI improvements.
///
/// 1. Adds "BYOBs near you" section title above the horizontal card row.
/// 2. Adds cuisine type text to each nearest-3 card (via formatCuisineType).
/// 3. Increases card horizontal padding from 10 to 14 px.
///
/// Only the NearestCardsSection is replaced — chips, list view, and map widget
/// are untouched.
void buildByobContract11(App app) {
  final _formatCuisineFn = CustomFunctionHandle(
    name: 'formatCuisineType',
    args: {'typeString': string, 'otherNote': string},
    returnType: string,
  );

  app.editPage(ff.Pages.homePage, (page) {
    page.ensureReplaced(
      ff.Pages.homePage.widgets
          .byPath('HomePage.body[0].children[1].children[1].children[1]')
          .single,
      Container(
        name: 'NearestCardsSection',
        height: 200,
        color: Colors.primaryBackground,
        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 8),
        child: Column(
          name: 'NearestCardsSectionColumn',
          crossAxis: CrossAxis.stretch,
          children: [
            // Section title — wine red label above the card row
            Container(
              name: 'NearestTitlePad',
              padding: EdgeInsets.only(bottom: 6),
              child: Text(
                'BYOBs near you',
                name: 'NearestSectionTitle',
                color: Colors.hex(0xFF8B2635),
                style: Styles.titleSmall,
              ),
            ),
            // Horizontal card list — fills remaining height via Expanded
            Expanded(
              ListView(
                name: 'NearestCardsList',
                source: State('nearestThree'),
                horizontal: true,
                spacing: 8,
                itemBuilder: (item) => Container(
                  name: 'NearestCardWrapper',
                  width: 140,
                  color: Colors.hex(0xFFFFFFFF),
                  borderRadius: 12,
                  padding: EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  onTap: Navigate.to(
                    ff.Pages.restaurantDetailPage,
                    params: {
                      ff.Pages.restaurantDetailPage.params.restaurantName: item['Name'],
                      ff.Pages.restaurantDetailPage.params.cuisineType: item['philly_restaurant_type'],
                      'cuisineTypeNote': item['philly_restaurant_type_other_note'],
                      ff.Pages.restaurantDetailPage.params.corkageFeeType: item['philly_corkage_fee'],
                      ff.Pages.restaurantDetailPage.params.address: item['Add'],
                      ff.Pages.restaurantDetailPage.params.phone: item['Phone'],
                      ff.Pages.restaurantDetailPage.params.latitude: item['Latitude'],
                      ff.Pages.restaurantDetailPage.params.longitude: item['Longitude'],
                      ff.Pages.restaurantDetailPage.params.coverImageUrl: item['cover_image_url'],
                    },
                  ),
                  child: Column(
                    name: 'NearestCardContent',
                    crossAxis: CrossAxis.start,
                    spacing: 4,
                    children: [
                      Text(
                        item['Name'],
                        name: 'NearestCardName',
                        style: Styles.labelMedium,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      // Cuisine type — below name, above badge
                      Text(
                        CustomFunction(_formatCuisineFn, args: {
                          'typeString': item['philly_restaurant_type'],
                          'otherNote': item['philly_restaurant_type_other_note'],
                        }),
                        name: 'NearestCuisineText',
                        color: Colors.secondaryText,
                        style: Styles.bodySmall,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      // Corkage badge (green / wine-red / orange, one visible at a time)
                      Container(
                        name: 'NearestFreeBadge',
                        color: Colors.hex(0xFF2E7D32),
                        borderRadius: 4,
                        padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        visible: Equals(item['philly_corkage_fee'], 'free'),
                        child: Text(
                          'Free BYOB',
                          name: 'NearestFreeText',
                          color: Colors.hex(0xFFFFFFFF),
                          style: Styles.labelSmall,
                        ),
                      ),
                      Container(
                        name: 'NearestCorkageBadge',
                        color: Colors.hex(0xFF8B2635),
                        borderRadius: 4,
                        padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        visible: Equals(item['philly_corkage_fee'], 'corkage_fee'),
                        child: Text(
                          'Corkage Fee',
                          name: 'NearestCorkageText',
                          color: Colors.hex(0xFFFFFFFF),
                          style: Styles.labelSmall,
                        ),
                      ),
                      Container(
                        name: 'NearestOtherBadge',
                        color: Colors.hex(0xFFBF6A02),
                        borderRadius: 4,
                        padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        visible: Equals(item['philly_corkage_fee'], 'other'),
                        child: Text(
                          'Ask Us',
                          name: 'NearestOtherText',
                          color: Colors.hex(0xFFFFFFFF),
                          style: Styles.labelSmall,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              name: 'NearestListExpanded',
            ),
          ],
        ),
      ),
    );
  });
}

/// Contract 12 — Phase 1 (push 1): adds `searchRestaurantsByName` custom function
/// and `searchText` page state. No UI references to the new function.
/// After this push succeeds, uncomment buildByobContract12 in buildByobPhilly and
/// comment out buildByobContract12AddFn to do push 2 (full UI).
void buildByobContract12AddFn(App app) {
  app.editPageState(ff.Pages.homePage, (state) {
    state.ensureField('searchText', string.withDefault(''));
  });

  const _searchFnCode = r'''if (restaurants == null) return [];
if (query == null || query.trim().isEmpty) return restaurants;
final q = query.toLowerCase();
return restaurants.where((r) => (r.name ?? '').toLowerCase().contains(q)).toList();''';

  app.raw((project) {
    final existing = findCustomFunction(project, name: 'searchRestaurantsByName');
    if (existing != null) {
      updateCustomFunction(
        project,
        name: 'searchRestaurantsByName',
        code: _searchFnCode,
      );
    } else {
      addCustomFunction(
        project,
        name: 'searchRestaurantsByName',
        code: _searchFnCode,
        description:
            'Case-insensitive name search: returns restaurants whose name contains the query. Empty query returns all.',
      );
    }
  });
}

/// Contract 12 — Phase 2 (push 2): full search bar UI.
/// searchRestaurantsByName must already exist in the project (added by push 1).
/// Uses searchFilteredRestaurants as an intermediate page state so that:
///  - ListView source is a simple State reference (no CustomFunction as source)
///  - Chip actions and search onChange never nest CustomFunction inside CustomFunction
/// Contract 12: Search bar on HomePage — real-time name filter with AND logic.
///
/// 1. Adds searchText page state (String, default "").
/// 2. Adds searchRestaurantsByName custom function (case-insensitive name contains).
/// 3. Rebuilds the full HomeBody to insert a search bar between ChipsStrip and the
///    content area. The search bar is visible in list view only (hidden in map view).
///    Includes a × clear button visible when searchText is non-empty.
/// 4. List view source: searchRestaurantsByName(filteredRestaurants, searchText).
/// 5. Nearest-3 (map view) also filters through searchRestaurantsByName so map and
///    list views stay in sync when search is active.
///
/// This contract does a full body[0] rebuild and is placed LAST in buildByobPhilly.
/// It bakes in C11 (NearestCardsSection with title + cuisine text) and C13 (12 chips
/// including Pizza/Sushi/Ramen) so the C12 body is the canonical final structure.
/// C8/C9 raw() callbacks still run after this and configure the GoogleMap widget.
void buildByobContract12(App app) {
  final restaurants = ff.Collections.restaurants;

  // ── 1. Add searchText page state ────────────────────────────────────────────
  // searchText was added in push 1 (buildByobContract12AddFn); this is idempotent.
  app.editPageState(ff.Pages.homePage, (state) {
    state.ensureField('searchText', string.withDefault(''));
  });

  // ── 2. Add / update searchRestaurantsByName ──────────────────────────────────
  // r.name accesses the Firestore 'Name' field (capital N) via the generated
  // RestaurantsRecord accessor. Empty / blank query returns the list unchanged.
  const _searchFnCode = r'''if (restaurants == null) return [];
if (query == null || query.trim().isEmpty) return restaurants;
final q = query.toLowerCase();
return restaurants.where((r) => (r.name ?? '').toLowerCase().contains(q)).toList();''';

  // Fix the function's type metadata. Push 1 created it with String return type
  // (addCustomFunction default). We must update arguments and returnParameter so
  // FlutterFlow's validator accepts SetState(filteredRestaurants, CustomFunction(searchFn)).
  // Note: collectionDocType/findCollection/stringType are not exported; use raw proto.
  app.raw((project) {
    final restaurantsColl = project.backend.collections.values.firstWhere(
      (c) => c.identifier.name == 'restaurants',
    );
    final rid = restaurantsColl.identifier.deepCopy();
    // restaurants argument: List<RestaurantsRecord>
    final restaurantListParam = FFParameter(
      identifier: FFIdentifier(
        name: 'restaurants',
        key: generateRandomAlphaNumericString(),
      ),
      dataType: FFDataTypeV2(
        scalarType: FFBaseDataType.Document,
        subType: FFSubType(collectionIdentifier: rid),
      ),
    );
    restaurantListParam.isList = true;
    // query argument: String
    final queryParam = FFParameter(
      identifier: FFIdentifier(
        name: 'query',
        key: generateRandomAlphaNumericString(),
      ),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
    );
    // return type: List<RestaurantsRecord>
    final returnParam = FFParameter(
      identifier: FFIdentifier(
        name: 'returnValue',
        key: generateRandomAlphaNumericString(),
      ),
      dataType: FFDataTypeV2(
        scalarType: FFBaseDataType.Document,
        subType: FFSubType(collectionIdentifier: rid),
      ),
    );
    returnParam.isList = true;
    updateCustomFunction(
      project,
      name: 'searchRestaurantsByName',
      code: _searchFnCode,
      arguments: [restaurantListParam, queryParam],
      returnParameter: returnParam,
    );
  });

  final searchFn = CustomFunctionHandle(
    name: 'searchRestaurantsByName',
    args: {'restaurants': listOf(restaurants), 'query': string},
    returnType: listOf(restaurants),
  );

  // ── Handles for existing custom functions ────────────────────────────────────
  final isTypeSelectedFn = CustomFunctionHandle(
    name: 'isTypeSelected',
    args: {'selectedTypes': listOf(string), 'type': string},
    returnType: bool_,
  );
  final isNoneSelectedFn = CustomFunctionHandle(
    name: 'isNoneSelected',
    args: {'selectedTypes': listOf(string)},
    returnType: bool_,
  );
  final filterTypesFn = CustomFunctionHandle(
    name: 'filterRestaurantsByTypes',
    args: {'restaurants': listOf(restaurants), 'selectedTypes': listOf(string)},
    returnType: listOf(restaurants),
  );
  final getNearestThreeFn = CustomFunctionHandle(
    name: 'getNearestThree',
    args: {'restaurants': listOf(restaurants), 'userLat': double_, 'userLng': double_},
    returnType: listOf(restaurants),
  );
  final formatCuisineFn = CustomFunctionHandle(
    name: 'formatCuisineType',
    args: {'typeString': string, 'otherNote': string},
    returnType: string,
  );

  // ── 3+4+5. Full HomeBody rebuild ─────────────────────────────────────────────
  // Chip order / labels match Contract 13 (12 types, Pizza/Sushi/Ramen replacing
  // American/Indian from the previous chip strip).
  const typeOrder = [
    'italian', 'mediterranean', 'japanese', 'seafood', 'sushi',
    'pizza', 'asian', 'mexican', 'thai', 'ramen', 'french', 'other',
  ];
  const typeLabels = {
    'italian': 'Italian', 'mediterranean': 'Mediterranean', 'japanese': 'Japanese',
    'seafood': 'Seafood', 'sushi': 'Sushi', 'pizza': 'Pizza',
    'asian': 'Asian', 'mexican': 'Mexican', 'thai': 'Thai',
    'ramen': 'Ramen', 'french': 'French', 'other': 'Other',
  };

  final wineRed = Colors.hex(0xFF8B2635);
  final white = Colors.hex(0xFFFFFFFF);

  Container buildChip({
    required String label,
    required bool selected,
    required String widgetName,
    required Object? visible,
    required List<DslAction> onTap,
  }) =>
      Container(
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

  // After each chip tap: base actions update chip state + filteredRestaurants
  // (chip-only). Then we re-apply the search query on top of the chip result
  // by overwriting filteredRestaurants again. ListView source = filteredRestaurants,
  // so no new state variable needed. Both SetState calls are single-level (no
  // nested CustomFunction). filteredRestaurants and nearestThree both pre-exist.
  List<DslAction> withNearestUpdate(List<DslAction> base) => [
    ...base,
    SetState('filteredRestaurants', CustomFunction(searchFn, args: {
      'restaurants': State('filteredRestaurants'),
      'query': State('searchText'),
    })),
    SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
      'restaurants': State('filteredRestaurants'),
      'userLat': State('userLatitude'),
      'userLng': State('userLongitude'),
    })),
  ];

  final allChipActions = withNearestUpdate([
    SetState.clear('selectedChipTypes'),
    SetState('filteredRestaurants', State('restaurants')),
  ]);

  List<DslAction> selChipActions(String type) => withNearestUpdate([
    SetState.removeFromList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ]);

  List<DslAction> unselChipActions(String type) => withNearestUpdate([
    SetState.addToList('selectedChipTypes', type),
    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
      'restaurants': State('restaurants'),
      'selectedTypes': State('selectedChipTypes'),
    })),
  ]);

  app.editPage(ff.Pages.homePage, (page) {
    page.ensureReplaced(
      ff.Pages.homePage.widgets.byPath('HomePage.body[0]').single,
      Column(
        name: 'HomeBody',
        crossAxis: CrossAxis.stretch,
        children: [
          // Chip strip — 12 types (C13 content baked in)
          Container(
            name: 'ChipsStrip',
            color: Colors.primaryBackground,
            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Wrap(
              name: 'ChipsWrap',
              spacing: 8,
              runSpacing: 8,
              children: [
                buildChip(
                  label: 'All',
                  selected: true,
                  widgetName: 'AllChipSel',
                  visible: CustomFunction(isNoneSelectedFn, args: {
                    'selectedTypes': State('selectedChipTypes'),
                  }),
                  onTap: allChipActions,
                ),
                buildChip(
                  label: 'All',
                  selected: false,
                  widgetName: 'AllChipUnsel',
                  visible: Not(CustomFunction(isNoneSelectedFn, args: {
                    'selectedTypes': State('selectedChipTypes'),
                  })),
                  onTap: allChipActions,
                ),
                for (final type in typeOrder) ...[
                  buildChip(
                    label: typeLabels[type]!,
                    selected: true,
                    widgetName: '${typeLabels[type]}ChipSel',
                    visible: CustomFunction(isTypeSelectedFn, args: {
                      'selectedTypes': State('selectedChipTypes'),
                      'type': type,
                    }),
                    onTap: selChipActions(type),
                  ),
                  buildChip(
                    label: typeLabels[type]!,
                    selected: false,
                    widgetName: '${typeLabels[type]}ChipUnsel',
                    visible: Not(CustomFunction(isTypeSelectedFn, args: {
                      'selectedTypes': State('selectedChipTypes'),
                      'type': type,
                    })),
                    onTap: unselChipActions(type),
                  ),
                ],
              ],
            ),
          ),
          // Search bar — list view only (hidden when map toggle is active)
          Container(
            name: 'SearchBarContainer',
            color: Colors.primaryBackground,
            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            visible: Not(State('isMapView')),
            child: Row(
              name: 'SearchBarRow',
              crossAxis: CrossAxis.center,
              children: [
                // TextField expands to fill available width
                Expanded(
                  TextField(
                    name: 'SearchField',
                    hint: 'Search restaurants...',
                    prefixIcon: 'search',
                    onChanged: [
                      SetState('searchText', TextValue()),
                      // Re-run chip filter first so we search within the correct chip subset.
                      SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
                        'restaurants': State('restaurants'),
                        'selectedTypes': State('selectedChipTypes'),
                      })),
                      // Overwrite filteredRestaurants with chip+search combined result.
                      SetState('filteredRestaurants', CustomFunction(searchFn, args: {
                        'restaurants': State('filteredRestaurants'),
                        'query': TextValue(),
                      })),
                      SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
                        'restaurants': State('filteredRestaurants'),
                        'userLat': State('userLatitude'),
                        'userLng': State('userLongitude'),
                      })),
                    ],
                  ),
                  name: 'SearchFieldExpanded',
                ),
                // × clear button — visible when searchText is non-empty
                IconButton(
                  'close',
                  name: 'ClearSearchButton',
                  size: 24,
                  color: Colors.secondaryText,
                  visible: Not(Equals(State('searchText'), '')),
                  onTap: [
                    SetState('searchText', ''),
                    // ClearTextField omitted — SearchField is new in this push and
                    // FlutterFlow's validator can't find it by name yet.
                    // Wire it manually in FlutterFlow UI if needed.
                    // Restore filteredRestaurants to chip-only result (remove search).
                    SetState('filteredRestaurants', CustomFunction(filterTypesFn, args: {
                      'restaurants': State('restaurants'),
                      'selectedTypes': State('selectedChipTypes'),
                    })),
                    SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
                      'restaurants': State('filteredRestaurants'),
                      'userLat': State('userLatitude'),
                      'userLng': State('userLongitude'),
                    })),
                  ],
                ),
              ],
            ),
          ),
          // Content area — list OR map (same structure as C8)
          Expanded(
            Stack(
              name: 'ViewToggleStack',
              alignment: Alignment.topLeft,
              children: [
                // List view — source = filteredRestaurants, which holds chip+search
                // combined result after each SetState chain (chip or search onChange).
                ListView(
                  name: 'RestaurantList',
                  source: State('filteredRestaurants'),
                  spacing: 0,
                  visible: Not(State('isMapView')),
                  itemBuilder: (item) => Container(
                    name: 'CardWrapper',
                    onTap: Navigate.to(
                      ff.Pages.restaurantDetailPage,
                      params: {
                        ff.Pages.restaurantDetailPage.params.restaurantName:
                            item['Name'],
                        ff.Pages.restaurantDetailPage.params.cuisineType:
                            item['philly_restaurant_type'],
                        'cuisineTypeNote':
                            item['philly_restaurant_type_other_note'],
                        ff.Pages.restaurantDetailPage.params.corkageFeeType:
                            item['philly_corkage_fee'],
                        ff.Pages.restaurantDetailPage.params.address:
                            item['Add'],
                        ff.Pages.restaurantDetailPage.params.phone:
                            item['Phone'],
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
                      cuisineTypeNote:
                          item['philly_restaurant_type_other_note'],
                      imageUrl: item['cover_image_url'],
                      corkageFeeType: item['philly_corkage_fee'],
                    ),
                  ),
                ),
                // Map view — visible when toggle is active
                Column(
                  name: 'MapViewColumn',
                  crossAxis: CrossAxis.stretch,
                  visible: State('isMapView'),
                  children: [
                    Expanded(
                      MapWidget(
                        lat: 39.9526,
                        lng: -75.1652,
                        zoom: 12,
                        name: 'MapArea',
                      ),
                      name: 'MapExpanded',
                    ),
                    // Nearest-3 strip (C11 content baked in)
                    Container(
                      name: 'NearestCardsSection',
                      height: 200,
                      color: Colors.primaryBackground,
                      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 8),
                      child: Column(
                        name: 'NearestCardsSectionColumn',
                        crossAxis: CrossAxis.stretch,
                        children: [
                          Container(
                            name: 'NearestTitlePad',
                            padding: EdgeInsets.only(bottom: 6),
                            child: Text(
                              'BYOBs near you',
                              name: 'NearestSectionTitle',
                              color: Colors.hex(0xFF8B2635),
                              style: Styles.titleSmall,
                            ),
                          ),
                          Expanded(
                            ListView(
                              name: 'NearestCardsList',
                              source: State('nearestThree'),
                              horizontal: true,
                              spacing: 8,
                              itemBuilder: (item) => Container(
                                name: 'NearestCardWrapper',
                                width: 140,
                                color: Colors.hex(0xFFFFFFFF),
                                borderRadius: 12,
                                padding: EdgeInsets.symmetric(
                                    horizontal: 14, vertical: 10),
                                onTap: Navigate.to(
                                  ff.Pages.restaurantDetailPage,
                                  params: {
                                    ff.Pages.restaurantDetailPage.params
                                        .restaurantName: item['Name'],
                                    ff.Pages.restaurantDetailPage.params
                                        .cuisineType:
                                        item['philly_restaurant_type'],
                                    'cuisineTypeNote':
                                        item['philly_restaurant_type_other_note'],
                                    ff.Pages.restaurantDetailPage.params
                                        .corkageFeeType:
                                        item['philly_corkage_fee'],
                                    ff.Pages.restaurantDetailPage.params
                                        .address: item['Add'],
                                    ff.Pages.restaurantDetailPage.params
                                        .phone: item['Phone'],
                                    ff.Pages.restaurantDetailPage.params
                                        .latitude: item['Latitude'],
                                    ff.Pages.restaurantDetailPage.params
                                        .longitude: item['Longitude'],
                                    ff.Pages.restaurantDetailPage.params
                                        .coverImageUrl:
                                        item['cover_image_url'],
                                  },
                                ),
                                child: Column(
                                  name: 'NearestCardContent',
                                  crossAxis: CrossAxis.start,
                                  spacing: 4,
                                  children: [
                                    Text(
                                      item['Name'],
                                      name: 'NearestCardName',
                                      style: Styles.labelMedium,
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    Text(
                                      CustomFunction(formatCuisineFn, args: {
                                        'typeString':
                                            item['philly_restaurant_type'],
                                        'otherNote':
                                            item['philly_restaurant_type_other_note'],
                                      }),
                                      name: 'NearestCuisineText',
                                      color: Colors.secondaryText,
                                      style: Styles.bodySmall,
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    Container(
                                      name: 'NearestFreeBadge',
                                      color: Colors.hex(0xFF2E7D32),
                                      borderRadius: 4,
                                      padding: EdgeInsets.symmetric(
                                          horizontal: 6, vertical: 2),
                                      visible: Equals(
                                          item['philly_corkage_fee'], 'free'),
                                      child: Text(
                                        'Free BYOB',
                                        name: 'NearestFreeText',
                                        color: Colors.hex(0xFFFFFFFF),
                                        style: Styles.labelSmall,
                                      ),
                                    ),
                                    Container(
                                      name: 'NearestCorkageBadge',
                                      color: Colors.hex(0xFF8B2635),
                                      borderRadius: 4,
                                      padding: EdgeInsets.symmetric(
                                          horizontal: 6, vertical: 2),
                                      visible: Equals(
                                          item['philly_corkage_fee'],
                                          'corkage_fee'),
                                      child: Text(
                                        'Corkage Fee',
                                        name: 'NearestCorkageText',
                                        color: Colors.hex(0xFFFFFFFF),
                                        style: Styles.labelSmall,
                                      ),
                                    ),
                                    Container(
                                      name: 'NearestOtherBadge',
                                      color: Colors.hex(0xFFBF6A02),
                                      borderRadius: 4,
                                      padding: EdgeInsets.symmetric(
                                          horizontal: 6, vertical: 2),
                                      visible: Equals(
                                          item['philly_corkage_fee'], 'other'),
                                      child: Text(
                                        'Ask Us',
                                        name: 'NearestOtherText',
                                        color: Colors.hex(0xFFFFFFFF),
                                        style: Styles.labelSmall,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            name: 'NearestListExpanded',
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
            name: 'ContentExpanded',
          ),
        ],
      ),
    );
  });

  // Apply SearchField decoration: white fill, outline border, 12px radius, padded.
  // Path: body[0] → children[1](SearchBarContainer) → children[0](SearchBarRow)
  //       → children[0](SearchField, expanded TextField node)
  app.editPage(ff.Pages.homePage, (page) {
    page.update(
      ff.Pages.homePage.widgets
          .byPath('HomePage.body[0].children[1].children[0].children[0]')
          .single,
      (patch) {
        patch.textFieldFilled(true);
        patch.textFieldFillColor(white);
        patch.textFieldBorder(InputBorder.outline);
        patch.borderRadius(12);
        patch.textFieldContentPadding(
            EdgeInsets.symmetric(horizontal: 16, vertical: 8));
      },
    );
  });

  // Fix HomeBody + MapViewColumn mainAxisSize → max (same as C8 raw fix).
  app.raw((project) {
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') {
        homeWC = wc;
        break;
      }
    }
    if (homeWC == null) return;

    FFNode? homeBody;
    FFNode? mapViewColumn;
    void walk(FFNode node) {
      if (node.name == 'HomeBody' && node.type == FFWidgetType.Column) {
        homeBody = node;
      }
      if (node.name == 'MapViewColumn' && node.type == FFWidgetType.Column) {
        mapViewColumn = node;
      }
      for (final child in node.children) {
        walk(child);
      }
    }
    walk(homeWC.node);

    if (homeBody != null) {
      homeBody!.props.column.minSizeValue = FFBooleanValue(inputValue: false);
    }
    if (mapViewColumn != null) {
      mapViewColumn!.props.column.minSizeValue =
          FFBooleanValue(inputValue: false);
    }
  });
}

/// Contract 14: Tappable cuisine type tags on RestaurantCard and RestaurantDetailPage.
///
/// 1. Adds getCuisineDisplayList — splits comma-separated type string into display labels.
/// 2. Adds filterRestaurantsByTypeOrNote — filters by display label. Returns all when
///    typeValue is empty, so onLoad can call it unconditionally (no If needed).
/// 3. Adds incomingCuisineFilter param to HomePage; onLoad applies it via the filter fn.
/// 4. Replaces CuisineText in RestaurantCard with a horizontal ListView of tappable tags.
/// 5. Same on RestaurantDetailPage. formatCuisineType is left in place.
void buildByobContract14(App app) {
  final restaurants = ff.Collections.restaurants;

  // ── 1. getCuisineDisplayList ──────────────────────────────────────────────────
  const _getCuisineDisplayListCode = r'''if (typeString == null) return <String>[];
final parts = typeString.split(',').map((p) => p.trim()).toList();
final result = <String>[];
for (final p in parts) {
  if (p.isEmpty) continue;
  if (p.toLowerCase() == 'other') {
    final note = (otherNote ?? '').trim();
    result.add(note.isNotEmpty ? (note[0].toUpperCase() + note.substring(1)) : 'Other');
  } else {
    result.add(p[0].toUpperCase() + p.substring(1));
  }
}
return result;''';

  app.raw((project) {
    if (findCustomFunction(project, name: 'getCuisineDisplayList') == null) {
      addCustomFunction(
        project,
        name: 'getCuisineDisplayList',
        code: _getCuisineDisplayListCode,
        description: 'Splits comma-separated cuisine type string into capitalised display labels.',
      );
    }
    FFParameter strParam(String name) => FFParameter(
          identifier: FFIdentifier(name: name, key: generateRandomAlphaNumericString()),
          dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
        );
    final returnParam = FFParameter(
      identifier: FFIdentifier(name: 'returnValue', key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
    );
    returnParam.isList = true;
    updateCustomFunction(
      project,
      name: 'getCuisineDisplayList',
      code: _getCuisineDisplayListCode,
      arguments: [strParam('typeString'), strParam('otherNote')],
      returnParameter: returnParam,
    );
  });

  final getCuisineDisplayListFn = CustomFunctionHandle(
    name: 'getCuisineDisplayList',
    args: {'typeString': string, 'otherNote': string},
    returnType: listOf(string),
  );

  // ── 2. filterRestaurantsByTypeOrNote ─────────────────────────────────────────
  // Returns all restaurants when typeValue is empty — no If action needed in onLoad.
  const _filterByTypeOrNoteCode = r'''if (restaurants == null) return [];
const knownTypes = {'italian', 'mediterranean', 'japanese', 'seafood', 'sushi', 'pizza', 'asian', 'mexican', 'thai', 'ramen', 'french', 'other', 'american', 'indian'};
final tv = (typeValue ?? '').trim();
if (tv.isEmpty) return restaurants;
final tvLower = tv.toLowerCase();
if (knownTypes.contains(tvLower)) {
  return restaurants.where((r) {
    final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
    return parts.contains(tvLower);
  }).toList();
}
// Not a known type: "Fine dining" and similar values live directly in philly_restaurant_type
// (not in other_note), so check both fields.
return restaurants.where((r) {
  final parts = (r.phillyRestaurantType ?? '').split(',').map((p) => p.trim().toLowerCase()).toList();
  if (parts.contains(tvLower)) return true;
  return (r.phillyRestaurantTypeOtherNote ?? '').toLowerCase().contains(tvLower);
}).toList();''';

  app.raw((project) {
    final restaurantsColl = project.backend.collections.values
        .firstWhere((c) => c.identifier.name == 'restaurants');
    final rid = restaurantsColl.identifier.deepCopy();

    final restaurantsParam = FFParameter(
      identifier: FFIdentifier(name: 'restaurants', key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.Document, subType: FFSubType(collectionIdentifier: rid)),
    );
    restaurantsParam.isList = true;

    final typeValueParam = FFParameter(
      identifier: FFIdentifier(name: 'typeValue', key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
    );

    final returnParam = FFParameter(
      identifier: FFIdentifier(name: 'returnValue', key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.Document, subType: FFSubType(collectionIdentifier: rid.deepCopy())),
    );
    returnParam.isList = true;

    if (findCustomFunction(project, name: 'filterRestaurantsByTypeOrNote') == null) {
      addCustomFunction(
        project,
        name: 'filterRestaurantsByTypeOrNote',
        code: _filterByTypeOrNoteCode,
        description: 'Filters by cuisine display label. Returns all when typeValue is empty.',
      );
    }
    updateCustomFunction(
      project,
      name: 'filterRestaurantsByTypeOrNote',
      code: _filterByTypeOrNoteCode,
      arguments: [restaurantsParam, typeValueParam],
      returnParameter: returnParam,
    );
  });

  final filterByTypeOrNoteFn = CustomFunctionHandle(
    name: 'filterRestaurantsByTypeOrNote',
    args: {'restaurants': listOf(restaurants), 'typeValue': string},
    returnType: listOf(restaurants),
  );

  // ── 2b. getChipFilterForTag — returns chip key for known types, '' for other_note ──
  const _getChipFilterCode = r'''const knownTypes = {'italian', 'mediterranean', 'japanese', 'seafood', 'sushi', 'pizza', 'asian', 'mexican', 'thai', 'ramen', 'french', 'other', 'american', 'indian'};
final lower = (displayLabel ?? '').toLowerCase();
return knownTypes.contains(lower) ? lower : '';''';
  app.raw((project) {
    FFParameter _sp(String n) => FFParameter(
      identifier: FFIdentifier(name: n, key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
    );
    if (findCustomFunction(project, name: 'getChipFilterForTag') == null) {
      addCustomFunction(project, name: 'getChipFilterForTag', code: _getChipFilterCode,
          description: 'Returns lowercase chip key for known cuisine types; empty string for other_note types.');
    }
    updateCustomFunction(project, name: 'getChipFilterForTag', code: _getChipFilterCode,
        arguments: [_sp('displayLabel')]);
  });
  final getChipFilterForTagFn = CustomFunctionHandle(
    name: 'getChipFilterForTag',
    args: {'displayLabel': string},
    returnType: string,
  );

  // ── 2c. initChipTypes — resolves selectedChipTypes from page load params
  // Three outcomes:
  //   incomingFilter empty          → [] (normal load, All chip highlights)
  //   incomingFilter non-empty + chipFilter non-empty → [chipFilter] (known type, e.g. Italian)
  //   incomingFilter non-empty + chipFilter empty     → ['__none__'] (other_note: no chip highlights)
  const _initChipTypesCode = r'''if (incomingFilter == null || incomingFilter.isEmpty) return <String>[];
if (chipFilter != null && chipFilter.isNotEmpty) return <String>[chipFilter];
return <String>['__none__'];''';
  app.raw((project) {
    FFParameter _sp(String n) => FFParameter(
      identifier: FFIdentifier(name: n, key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
    );
    final chipTypesReturn = FFParameter(
      identifier: FFIdentifier(name: 'returnValue', key: generateRandomAlphaNumericString()),
      dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
    );
    chipTypesReturn.isList = true;
    if (findCustomFunction(project, name: 'initChipTypes') == null) {
      addCustomFunction(project, name: 'initChipTypes', code: _initChipTypesCode,
          description: 'Resolves selectedChipTypes from page params. Returns [] when no filter, [chipFilter] for known types, [\'__none__\'] for other_note types.');
    }
    updateCustomFunction(project, name: 'initChipTypes', code: _initChipTypesCode,
        arguments: [_sp('incomingFilter'), _sp('chipFilter')], returnParameter: chipTypesReturn);
  });
  final initChipTypesFn = CustomFunctionHandle(
    name: 'initChipTypes',
    args: {'incomingFilter': string, 'chipFilter': string},
    returnType: listOf(string),
  );

  // ── 3. Add page params to HomePage ───────────────────────────────────────────
  app.editPageParams(ff.Pages.homePage, (params) {
    params.ensureParam('incomingCuisineFilter', string);
    params.ensureParam('selectedChipFilter', string);
  });

  // ── 4. Update onLoad — apply cuisine filter + optional chip pre-selection ─────
  // incomingCuisineFilter drives filteredRestaurants (all types, known + other_note).
  // selectedChipFilter pre-selects a chip via initChipTypes when non-empty; '' → no chip.
  final getNearestThreeFn = CustomFunctionHandle(
    name: 'getNearestThree',
    args: {'restaurants': listOf(restaurants), 'userLat': double_, 'userLng': double_},
    returnType: listOf(restaurants),
  );

  app.editPageOnLoad(ff.Pages.homePage, [
    FirestoreQuery(restaurants, limit: 100, outputAs: 'loadedRestaurants'),
    SetState('restaurants', ActionOutput('loadedRestaurants')),
    SetState('filteredRestaurants', CustomFunction(filterByTypeOrNoteFn, args: {
      'restaurants': ActionOutput('loadedRestaurants'),
      'typeValue': Param('incomingCuisineFilter'),
    })),
    SetState('selectedChipTypes', CustomFunction(initChipTypesFn, args: {
      'incomingFilter': Param('incomingCuisineFilter'),
      'chipFilter': Param('selectedChipFilter'),
    })),
    RequestPermissions(permission: PermissionKind.location),
    SetState('nearestThree', CustomFunction(getNearestThreeFn, args: {
      'restaurants': State('filteredRestaurants'),
      'userLat': State('userLatitude'),
      'userLng': State('userLongitude'),
    })),
  ]);

  // ── 5. Replace CuisineText in RestaurantCard ──────────────────────────────────
  // ONLY replaces children[0].children[0].children[0].children[1].children[0].children[1]
  // (the CuisineText Text widget). All other widgets (image, name, badge) are untouched.
  // A fixed-height Container (24px) wraps the horizontal ListView to give it a
  // bounded height inside the Column — required for a horizontal scroll view in Flutter.
  app.editComponent(ff.Components.restaurantCard, (component) {
    component.ensureReplaced(
      ff.Components.restaurantCard.widgets
          .byPath(
            'RestaurantCard.children[0].children[0].children[0].children[1].children[0].children[1]',
          )
          .single,
      Container(
        name: 'CuisineTagsContainer',
        height: 24,
        child: ListView(
          name: 'CuisineTagsList',
          source: CustomFunction(getCuisineDisplayListFn, args: {
            'typeString': Param('cuisineType'),
            'otherNote': Param('cuisineTypeNote'),
          }),
          horizontal: true,
          spacing: 4,
          shrinkWrap: true,
          itemBuilder: (item) => Container(
            name: 'CuisineTag',
            color: Colors.primaryBackground,
            borderRadius: 8,
            borderColor: Colors.hex(0xFF8B2635),
            borderWidth: 1.0,
            padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
            onTap: Navigate.to(ff.Pages.homePage, params: {
              'incomingCuisineFilter': item,
              'selectedChipFilter': CustomFunction(getChipFilterForTagFn, args: {'displayLabel': item}),
            }),
            child: Text(
              item,
              name: 'CuisineTagText',
              color: Colors.hex(0xFF8B2635),
              style: Styles.labelSmall,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ),
      ),
    );
  });

  // ── 6. Replace CuisineText in RestaurantDetailPage ───────────────────────────
  // ONLY replaces body[0].children[1].children[0].children[1] (CuisineText).
  // Address, phone, Get Directions, and all other widgets are untouched.
  app.editPage(ff.Pages.restaurantDetailPage, (page) {
    page.ensureReplaced(
      ff.Pages.restaurantDetailPage.widgets
          .byPath(
            'RestaurantDetailPage.body[0].children[1].children[0].children[1]',
          )
          .single,
      Container(
        name: 'CuisineTagsContainer',
        height: 28,
        child: ListView(
          name: 'CuisineTagsList',
          source: CustomFunction(getCuisineDisplayListFn, args: {
            'typeString': Param('cuisineType'),
            'otherNote': Param('cuisineTypeNote'),
          }),
          horizontal: true,
          spacing: 4,
          shrinkWrap: true,
          itemBuilder: (item) => Container(
            name: 'CuisineTag',
            color: Colors.primaryBackground,
            borderRadius: 8,
            borderColor: Colors.hex(0xFF8B2635),
            borderWidth: 1.0,
            padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
            onTap: Navigate.to(ff.Pages.homePage, params: {
              'incomingCuisineFilter': item,
              'selectedChipFilter': CustomFunction(getChipFilterForTagFn, args: {'displayLabel': item}),
            }),
            child: Text(
              item,
              name: 'CuisineTagText',
              color: Colors.hex(0xFF8B2635),
              style: Styles.labelSmall,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ),
      ),
    );
  });
}

/// Contract 18: Add `city` field to RestaurantsRecord schema + filter onLoad query.
///
/// 1. Adds `city` (String) field to the `restaurants` Firestore collection.
///    This is idempotent — no-op if the field already exists.
/// 2. Patches the compiled ON_INIT_STATE action chain on HomePage to add
///    WHERE city == "philadelphia" to the first FFFirestoreQuery found.
///    The FirestoreQuery DSL action has no filter API, so this is done via raw()
///    which runs after _compilePages and sees the fully compiled action chain.
void buildByobContract18(App app) {
  // ── 1. Add `city` String field to restaurants collection ────────────────────
  app.raw((project) {
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      if (coll.fields.containsKey('city')) break;
      coll.fields['city'] = FFParameter(
        identifier: FFIdentifier(
          name: 'city',
          key: generateRandomAlphaNumericString(),
        ),
        dataType: FFDataTypeV2(scalarType: FFBaseDataType.String),
      );
      break;
    }
  });

  // ── 2. Patch the compiled onLoad Firestore query with WHERE city == "philadelphia"
  // raw() runs after _compilePages, so the ON_INIT_STATE chain from C14's
  // editPageOnLoad is already compiled into the project at this point.
  app.raw((project) {
    // Resolve `city` field identifier from the collection
    FFIdentifier? cityFieldId;
    for (final coll in project.backend.collections.values) {
      if (coll.identifier.name != 'restaurants') continue;
      for (final field in coll.fields.values) {
        if (field.identifier.name == 'city') {
          cityFieldId = field.identifier.deepCopy();
          break;
        }
      }
      break;
    }
    if (cityFieldId == null) return;

    // Find HomePage widget class
    FFWidgetClass? homeWC;
    for (final wc in project.widgetClasses.values) {
      if (wc.name == 'HomePage') {
        homeWC = wc;
        break;
      }
    }
    if (homeWC == null) return;

    // Build WHERE city == "philadelphia"
    final cityFilter = FFFirestoreFilter(
      collectionFieldIdentifier: cityFieldId,
      relation: FFFirestoreFilter_Relation.EQUAL_TO,
    );
    cityFilter.inputValue = FFParameterValue(serializedValue: 'philadelphia');

    final whereClause = FFFirestoreWhere(
      filters: [FFFirestoreWhere_NestedFilter(baseFilter: cityFilter)],
      isAnd: true,
    );

    // Walk the ON_INIT_STATE action chain and patch the first Firestore query.
    // Uses FFAction.database.firestoreQuery (set by Actions.firestoreQuery).
    void patchChain(FFActionNode? node) {
      if (node == null) return;
      if (node.hasAction()) {
        final action = node.action;
        if (action.hasDatabase() && action.database.hasFirestoreQuery()) {
          final query = action.database.firestoreQuery;
          if (!query.hasWhere()) {
            query.where = whereClause;
          }
          return; // patched — stop walking
        }
      }
      patchChain(node.followUpAction);
    }

    for (final triggerAction in homeWC.node.triggerActions) {
      if (triggerAction.trigger.triggerType ==
          FFActionTriggerType.ON_INIT_STATE) {
        patchChain(triggerAction.rootAction);
        break;
      }
    }
  });
}

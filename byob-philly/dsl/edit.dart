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
BYOB Finder Philadelphia — all contracts (1 + 2).

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

/// Top-level builder — applies Contract 1 then Contract 2 in one idempotent run.
void buildByobPhilly(App app) {
  buildByobContract1(app);
  buildByobContract2(app);
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

  // ── 5. Page-load Firestore query ─────────────────────────────────────────────
  app.editPageOnLoad(ff.Pages.homePage, [
    FirestoreQuery(restaurants, limit: 100, outputAs: 'loadedRestaurants'),
    SetState('restaurants', ActionOutput('loadedRestaurants')),
  ]);

  // ── 6. HomePage AppBar + ListView binding ───────────────────────────────────
  // Extract real collection field identifiers from the compiled project before
  // editPage runs. app.raw() callbacks execute in registration order, and
  // editPage internally registers its own raw() at the end — so this raw()
  // fires first, populating the outer variables before mutateNode's closure runs.
  FFIdentifier? _fieldIdName;
  FFIdentifier? _fieldIdRestaurantType;
  FFIdentifier? _fieldIdCoverImage;
  FFIdentifier? _fieldIdCorkageFeeType;
  FFIdentifier? _fieldIdCorkageFeeAmount;

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
    _fieldIdCorkageFeeAmount = findFieldId('corkage_fee_amount');
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
        // 1. Bind the ListView to page-state 'restaurants'.
        listView.generatorVariable = FFGeneratorVariable(
          identifier: FFIdentifier(
            name: 'restaurant',
            key: generateRandomAlphaNumericString(),
          ),
          variable: FFVariable(
            source: FFVariableSource.LOCAL_STATE,
            nodeKeyRef: FFNodeKeyReference(key: 'Scaffold_oa99nxk6'),
            baseVariable: FFBaseVariable(
              localState: FFLocalStateVariable(
                fieldIdentifier: FFIdentifier(
                  name: 'restaurants',
                  key: 'j1pgjuv0',
                ),
                stateVariableType: FFStateVariableType.WIDGET_CLASS_STATE,
              ),
            ),
          ),
        );

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
        bindParam('2tbm0tpd', 'corkageFeeAmount', _fieldIdCorkageFeeAmount);
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
  app.editComponent(ff.Components.restaurantCard, (component) {
    // ── 1. Add subtle card shadow ────────────────────────────────────────────
    // Target: Container_7hjqc9u1 — the white card container (bg + borderRadius)
    component.update(
      ff.Components.restaurantCard.widgets
          .byPath('RestaurantCard.children[0].children[0]')
          .single,
      (patch) {
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
                      // Variant 2: Corkage fee amount — gray
                      Container(
                        name: 'CorkageFeeBadge',
                        color: Colors.hex(0xFF616161),
                        borderRadius: 6,
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        visible: Equals(Param('corkageFeeType'), 'corkage_fee'),
                        child: Text(
                          Param('corkageFeeAmount'),
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

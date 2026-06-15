library;

import 'dart:io';

import 'package:flutterflow_ai/flutterflow_ai.dart';
import 'package:byob_philly/flutterflow_project.dart' as ff;

Future<void> main(List<String> args) async {
  final options = _parseCliOptions(args);
  try {
    await flutterFlowAI(
      buildByobContract1,
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
Contract 1: BYOB Finder Philadelphia — Theme + Firebase + Restaurant List.

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

  // ── 6. HomePage AppBar title ────────────────────────────────────────────────
  // The ListView body with RestaurantCard bindings was established in a prior
  // push and is already correct. Only the AppBar title needs idempotent update.
  app.editPage(ff.Pages.homePage, (page) {
    page.update(
      ff.Pages.homePage.widgets
          .byPath('HomePage.appBar[0].title[0]')
          .single,
      (patch) {
        patch.text('BYOB Finder Philadelphia');
      },
    );
  });

}

<html>

<head>
  <title>PHP Test</title>
</head>

<body>
  <?php
  require __DIR__ . '/../vendor/autoload.php';

  use Logto\Sdk\LogtoClient;
  use Logto\Sdk\LogtoConfig;
  use Logto\Sdk\Constants\UserScope;
  use Logto\Sdk\InteractionMode;
  use Logto\Sdk\Models\DirectSignInOptions;
  use Logto\Sdk\Constants\DirectSignInMethod;
  use Logto\Sdk\Constants\FirstScreen;
  use Logto\Sdk\Constants\AuthenticationIdentifier;
  use Logto\Sdk\Oidc\OidcCore;

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
  $dotenv->load();

  // Set the SSL verification options for PHP before creating the LogtoClient
  $contextOptions = [
    'ssl' => [
      'verify_peer' => false,
      'verify_peer_name' => false,
    ],
  ];
  stream_context_set_default($contextOptions);

  $resources = ['https://default.logto.app/api', 'https://shopping.api'];
  $client = new LogtoClient(
    new LogtoConfig(
      endpoint: $_ENV['LOGTO_ENDPOINT'],
      appId: $_ENV['LOGTO_APP_ID'],
      appSecret: $_ENV['LOGTO_APP_SECRET'],
      // resources: $resources, // Uncomment this line to specify resources
      scopes: [UserScope::email, UserScope::organizations, UserScope::organizationRoles], // Update per your needs
    )
  );

  switch (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) {
    case '/':
    case null:
      if (!$client->isAuthenticated()) {
        // show different sign in options
        echo '<h3>Sign In Options:</h3>';
        echo '<ul>';
        echo '<li><a href="/sign-in">Normal Sign In</a></li>';
        echo '<li><a href="/sign-in/sign-up">Sign In (Sign Up First)</a></li>';
        echo '<li><a href="/sign-in/social">Sign In with GitHub</a></li>';
        echo '<li><a href="/sign-in/email-and-username">Sign In with Email and Username</a></li>';
        echo '</ul>';
        break;
      }

      echo '<a href="organizations">View organization token</a><br/>';
      echo '<a href="sign-out">Sign out</a>';
      echo '<h2>Userinfo</h2>';
      echo '<pre>';
      echo var_export($client->fetchUserInfo(), true);
      echo '</pre>';
      echo '<h2>ID token claims</h2>';
      echo '<pre>';
      echo var_export($client->getIdTokenClaims(), true);
      echo '</pre><br>';
      // var_dump($client->getAccessTokenClaims($resources[0])); // Uncomment this line to see the access token claims
      break;
    
    case '/organizations':
      if (!$client->isAuthenticated()) {
        echo '<a href="/sign-in">Sign in</a>';
        break;
      }

      echo '<a href="sign-out">Sign out</a>';
      echo '<h2>Organization token claims</h2>';
      echo '<pre>';
      echo var_export($client->getOrganizationTokenClaims('<organization-id>'), true); // Replace <organization-id> with a valid organization ID
      echo '</pre>';
      break;

    case '/sign-in':
      header('Location: ' . $client->signIn("http://localhost:8080/sign-in-callback"));
      exit();

    case '/sign-in/sign-up':
      header('Location: ' . $client->signIn(
        "http://localhost:8080/sign-in-callback",
        interactionMode: InteractionMode::signUp
      ));
      exit();

    case '/sign-in/social':
      header('Location: ' . $client->signIn(
        "http://localhost:8080/sign-in-callback",
        directSignIn: new DirectSignInOptions(
          method: DirectSignInMethod::social,
          target: 'github'
        )
      ));
      exit();

    case '/sign-in/email-and-username':
      header('Location: ' . $client->signIn(
        "http://localhost:8080/sign-in-callback",
        firstScreen: FirstScreen::signIn,
        identifiers: [AuthenticationIdentifier::email, AuthenticationIdentifier::username]
      ));
      exit();

    case '/sign-in-callback':
      $client->handleSignInCallback();
      header('Location: /');
      exit();

    case '/sign-out':
      $to = $client->signOut('http://localhost:8080');
      header("Location: $to");
      exit();

    default:
      echo 'bad';
      break;
  }
  ?>
</body>

</html>

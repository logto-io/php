<html>

<head>
  <title>PHP Test</title>
</head>

<body>
  <?php
  require 'vendor/autoload.php';

  use Logto\Sdk\LogtoClient;
  use Logto\Sdk\LogtoConfig;

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  $resources = ['https://default.logto.app/api', 'https://shopping.api'];
  $client = new LogtoClient(
    new LogtoConfig(
      endpoint: "http://localhost:3001",
      appId: $_ENV['LOGTO_APP_ID'],
      appSecret: $_ENV['LOGTO_APP_SECRET'],
      resources: $resources,
      scopes: ['email'],
    )
  );

  switch ($_SERVER['PATH_INFO']) {
    case '/':
    case null:
      if (!$client->isAuthenticated()) {
        echo '<button onclick="location.href=\'/sign-in\'">Sign in</button>';
        break;
      }

      echo '<button onclick="location.href=\'/sign-out\'">Sign out</button>';
      echo '<br>';
      var_dump($client->fetchUserInfo());
      echo '<br>';
      var_dump($client->getIdTokenClaims());
      echo '<br>';
      var_dump($client->getAccessTokenClaims($resources[0]));
      break;

    case '/sign-in':
      header('Location: ' . $client->signIn("http://localhost:5000/callback"));
      exit();

    case '/callback':
      $client->handleSignInCallback();
      header('Location: /');
      exit();

    case '/sign-out':
      $to = $client->signOut('http://localhost:5000/');
      header("Location: $to");
      exit();

    default:
      echo 'bad';
      break;
  }
  ?>
</body>

</html>

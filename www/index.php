<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require('db.inc.php');
require('env.php');

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

$lang = "en";

if (isset($_GET["lang"])) {
    if (in_array($_GET["lang"], array("en", "nl"))) {
        $lang = $_GET["lang"];
    }
}


$languages = [
    'title' => [
        'nl' => 'Inschrijfformulier',
        'en' => 'Signup form'
    ],
    'intro' => [
        'nl' => 'lorem ipsum nl',
        'en' => 'lorem ipsum en'
    ],
    'label_firstname' => [
        'nl' => 'Voornaam',
        'en' => 'First name'
    ],
    'label_lastname' => [
        'nl' => 'Achternaam',
        'en' => 'Last name'
    ],
    'label_mail' => [
        'nl' => 'E-mailadres',
        'en' => 'Email'
    ],
    'label_country' => [
        'nl' => 'Land',
        'en' => 'Country'
    ],
    'error_firstname_required' => [
        'nl' => 'Voornaam is een verplicht veld.',
        'en' => 'First name is a required field.'
    ],
    'error_firstname_specialCharacters' => [
        'nl' => 'Voornaam mag geen speciale tekens bevatten.',
        'en' => 'First name can not contain special characters.'
    ],
    'error_lastname_required' => [
        'nl' => 'Achternaam is een verplicht veld.',
        'en' => 'Last name is a required field.'
    ],
    'error_lastname_specialCharacters' => [
        'nl' => 'Achternaam mag geen speciale tekens bevatten.',
        'en' => 'Last name can not contain special characters.'
    ],
    'error_username_required' => [
        'nl' => 'Username is een verplicht veld.',
        'en' => 'Username is a required field.'
    ],
    'error_username_length' => [
        'nl' => 'Username moet minstens 3 tekens lang zijn.',
        'en' => 'Username must contain at least 3 characters.'
    ],
    'error_username_specialCharacters' => [
        'nl' => 'Username moet bestaan uit letters, cijfers, en underscores.',
        'en' => 'Username must contain letters, numbers, and underscores.'
    ],
    'error_username_underscores' => [
        'nl' => 'Username mag niet beginnen en eindigen met een underscore.',
        'en' => 'Username can not start and end with an underscore.'
    ],
    'error_username_exist' => [
        'nl' => 'Username bestaat al.',
        'en' => 'Username already exists.'
    ],
    'error_mail_required' => [
        'nl' => 'E-mail is een verplicht veld',
        'en' => 'Email is required'
    ],
    'error_mail_valid' => [
        'nl' => 'E-mailadres niet correct.',
        'en' => 'Email not correct.'
    ],
    'error_country_required' => [
        'nl' => 'Land is een verplicht veld.',
        'en' => 'Country is required'
    ],
    'error_submit_newUser' => [
        'nl' => 'Er is iets verkeerd gelopen.',
        'en' => 'Something went.'
    ]
];

$errors = [
    'firstName' => [],
    'lastName' => [],
    'username' => [],
    'mail' => [],
    'country' => [],
    'terms' => []
];

$allErrors = array_merge(
    $errors['firstName'],
    $errors['lastName'],
    $errors['username'],
    $errors['mail'],
    $errors['country'],
    $errors['terms']
);

$firstname = "";
$lastname = "";
$username = "";
$mail = "";
$country = "";
$terms = 0;


if (isset($_POST['submit'])) {
    //firstName validation
    if (empty($_POST['firstName'])) {
        $errors['firstName'][] = $languages['error_firstname_required'][$lang];
    } else {
        $firstname = $_POST['firstName'];
        if (preg_match("/[^a-zA-Z\s'-]/", $firstname)) {
            $errors['firstName'][] = $languages['error_firstname_specialCharacters'][$lang];
        }
    }

    //lastName validation
    if (empty($_POST['lastName'])) {
        $errors['lastName'][] = $languages['error_lastname_required'][$lang];
    } else {
        $lastname = $_POST['lastName'];
        if (preg_match("/[^a-zA-Z\s'-]/", $lastname)) {
            $errors['lastName'][] = $languages['error_lastname_specialCharacters'][$lang];
        }
    }

    //username validation
    if (empty($_POST['username'])) {
        $errors['username'][] = $languages['error_username_required'][$lang];
    } else {
        $username = strtolower($_POST['username']);
        if (strlen($username) < 3) {
            $errors['username'][] = $languages['error_username_length'][$lang];
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username) || !preg_match('/_/', $username)) {
            $errors['username'][] = $languages['error_username_specialCharacters'][$lang];
        }
        if (!preg_match('/^(?!_).*(?<!_)$/', $username)) {
            $errors['username'][] = $languages['error_username_underscores'][$lang];
        }
        if (existingUsername($username) == true) {
            $errors['username'][] = $languages['error_username_exist'][$lang];
        }
    }

    //email validation
    if (empty($_POST['mail'])) {
        $errors['mail'][] = $languages['error_mail_required'][$lang];
    } else {
        $mail = $_POST['mail'];
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors['mail'][] = $languages['error_mail_valid'][$lang];
        }
    }

    //country validation
    if ($_POST['country'] == 0) {
        $errors['country'][] = $languages['error_country_required'][$lang];
    } else {
        $country = $_POST['country'];
    }

    //terms validation
    if (isset($_POST['terms'])) {
        $terms = 1;
    }

    if (count($allErrors) == 0) { // er werden geen fouten geregistreerd tijdens validatie
        $newUser = registerNewUser($firstname, $lastname, $username, $mail, $country, $terms);
        if (!$newUser) {
            $errors[] = $languages['error_submit_newUser'][$lang];
        } else {
            $_SESSION['message'] = "Welcome $firstname!";
            header("Location: index.php?lang=<?= $lang; ?>p");
            exit;
        }
    }
}

// if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
//     $uploadFileDir = 'avatars/';

//     $fileNameCmps = explode(".", $_FILES['avatar']['name']);
//     $fileExtension = strtolower(end($fileNameCmps));

//     if (!in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
//         $errors['file'] = $fileExtension . ' is not supported.';
//     } else {
//         if ($_FILES['avatar']['size'] > 1000000) {
//             $errors['file'] = 'The uploaded file is too big, 1MB max allowed.';
//         } else {
//             $image_info = getimagesize($_FILES["avatar"]["tmp_name"]);

//             if (($image_info[0] <= 1000) || ($image_info[1] <= 1000)) {
//                 $errors['file'] = 'The uploaded file too small, must be minimum 100x100.';
//             } else {
//                 $newFileName = '_' . generateRandomString(15) . '_' . time() . '.' . $fileExtension;
//                 $dest_path = $uploadFileDir . $newFileName;
//                 move_uploaded_file($_FILES['avatar']['tmp_name'], $dest_path);
//             }
//         }
//     }
// }

print '<pre>';
print_r($_POST);
// print_r($_FILES);
// print_r($errors);
print '</pre>';

?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <script src="/docs/5.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title><?= SITE_NAME; ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <link rel="stylesheet" href="styles/style.css">


    <meta name="theme-color" content="#712cf9">
</head>

<body class="bg-body-tertiary">

    <div class="container">
        <main>
            <div class="py-5 text-center">
                <img class="d-block mx-auto mb-4" src="https://getbootstrap.com/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">

                <h2><?= $languages['title'][$lang]; ?></h2>

                <p class="lead"><?= $languages['intro'][$lang]; ?></p>

                <?php if ($lang == 'en'): ?>
                    <a href="index.php?lang=nl">NL</a>
                <?php elseif ($lang == 'nl'): ?>
                    <a href="index.php?lang=en">EN</a>
                <?php endif; ?>
            </div>

            <div class="row g-5">


                <form method="post" action="index.php?lang=<?= $lang; ?>" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="firstName" class="form-label"><?= $languages['label_firstname'][$lang]; ?></label>
                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="" value="<?= $firstname ?>">
                            <?php if (count($errors['firstName'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul>
                                        <?php foreach ($errors['firstName'] as $error): ?>
                                            <li><?= $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="lastName" class="form-label"><?= $languages['label_lastname'][$lang]; ?></label>
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="" value="<?= $lastname; ?>">
                            <?php if (count($errors['lastName'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul>
                                        <?php foreach ($errors['lastName'] as $error): ?>
                                            <li><?= $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= $username; ?>">
                                <div class="invalid-feedback">
                                    Your username is required.
                                    Your username is invalid, it should only contain letters, numbers and an underscore.
                                    Your username is invalid, it can't start/end with an underscore.
                                    Your username is already in use.
                                </div>
                            </div>
                            <?php if (count($errors['username'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul>
                                        <?php foreach ($errors['username'] as $error): ?>
                                            <li><?= $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label" for="avatar">Profile picture (optional)</label>
                            <div class="input-group has-validation">
                                <input type="file" class="form-control<?= (isset($errors['file']) ? ' is-invalid' : ''); ?>" id="avatar" name="avatar" />
                                <div class="invalid-feedback">
                                    <?= $errors['file']; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="mail" class="form-label"><?= $languages['label_mail'][$lang]; ?></label>
                            <div class="input-group has-validation">
                                <input class="form-control" id="mail" name="mail" placeholder="you@example.com" value="<?= $mail; ?>">
                            </div>
                            <?php if (count($errors['mail'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul>
                                        <?php foreach ($errors['mail'] as $error): ?>
                                            <li><?= $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-12">
                                <label for="country" class="form-label"><?= $languages['label_country'][$lang]; ?></label>
                                <select class="form-select" id="country" name="country">
                                    <option <?= @$country == null ? 'selected' : ''; ?> value="0">Choose...</option>
                                    <option value="BE">Belgium</option>
                                    <option value="DM">Denmark</option>
                                    <option value="FR">France</option>
                                    <option value="DE">Germany</option>
                                    <option value="NL">Netherlands</option>
                                    <option value="PT">Portugal</option>
                                    <option value="SP">Spain</option>
                                </select>
                                <?php if (count($errors['country'])): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <ul>
                                            <?php foreach ($errors['country'] as $error): ?>
                                                <li><?= $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1">
                            <label class="form-check-label" for="terms">I agree with the terms and conditions.</label>
                        </div>
                        <input class="w-100 btn btn-primary btn-lg" type="submit" name="submit" id="submit" value="submit">
                </form>
            </div>
        </main>
    </div>

    <footer class="my-5 pt-5 text-body-secondary text-center text-small">
        <p class="mb-1">&copy; 2017–2024 Company Name</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
    </div>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="checkout.js"></script>
</body>

</html>
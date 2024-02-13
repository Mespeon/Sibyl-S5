<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=akshar:500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="container-fluid col-12 col-md-6">
        <div class="row py-4 border-bottom">
            <h1 class="fw-bolder text-primary">Cardinal</h1>
            <span class="text-dark" style="font-size: calc(1rem * 0.8) !important;">School Portal and Electronic Learning Management System</span>
        </div>
        <div class="row mt-md-4 mt-0 p-4 p-md-0 align-items-center justify-content-center">
            <div class="card bg-info border-0 mb-3 shadow-lg text-white">
                <div class="card-body fw-bold">
                    If you did not request for a password reset, do not continue or click any link in this email.
                </div>
            </div>
            <div class="card shadow-lg border-0 col-12">
                <div class="card-body">
                    <div class="card-title fs-3 fw-bold">Forgot Password</div>
                    <p class="card-text lh-sm">
                        Sometimes we tend to forget stuff, too.<br /><br />
                        You have requested to reset your password. Click on the button below to do so.
                    </p>
                    <div class="my-4">
                        <a role="button" class="btn btn-primary" href="http://192.168.1.127:3000/reset-password?t={{ $token }}">Reset Password</a>
                        <div class="mt-3" style="font-size: calc(1rem * 0.8) !important;">
                            <div>Can't see the button? Copy and paste the link below in your browser instead.</div>
                            <span>http://192.168.1.127:3000/reset-password?t={{ $token }}</span>
                        </div>
                    </div>
                    <p class="card-text fs-6 lh-sm">Remember to not share your passwords with anyone.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
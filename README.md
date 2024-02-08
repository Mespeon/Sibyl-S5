<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Generating a new public-private key pair

The steps below assume that a Linux subsystem is installed in a Windows PC.
If not, install it first then form the following.
If the PC is running Linux-based OS, proceed to step 2.
1. Open a terminal window then type ``bash``
2. Run ``openssl genrsa -aes256 -out private.pem 2048`` to generate a private key with password, or
3. Run ``openssl genrsa -out private.pem 2048`` to generate a private key without a password.
4. If you generated a private key with password, keep a copy of the password or always remember it as the keys will be rendered useless if the password is unusable.
5. Run ``openssl rsa -in private.pem -outform PEM -pubout -out public.pem`` to generate a public key from the private key.
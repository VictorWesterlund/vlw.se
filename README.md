# vlw.se
This is the source code behind [vlw.se](https://vlw.se) which has been written from the ground up by me. This website is built on top of my [Vegvisir web framework](https://github.com/victorwesterlund/vegvisir) and my [Reflect API framework](https://github.com/victorwesterlund/reflect).

# Installation
If you for whatever reason want to get this website up and running for yourself this is how that is done.

This website is built for PHP 8.0+ and MariaDB 14+ (for the API database).

**Confimed supported framework versions:**
Vegvisir|Reflect
--|--
✅ [`2.3.0`](https://github.com/VictorWesterlund/vegvisir/releases/tag/2.3.0)|✅ [`2.6.2`](https://github.com/VictorWesterlund/vegvisir/releases/tag/2.3.0)

## Website (Vegvisir)
1. **Download this repo**
   
   Git clone or download this repo to any local folder
   ```
   git clone https://github.com/VictorWesterlund/vlw.se
   ```
2. **Download and install Vegvisir**
   
   Follow the installation instructions for [Vegvisir](https://github.com/victorwesterlund/vegvisir) and point the `site_path` variable to the local vlw.se folder.

3. **Install dependencies**

   Install dependencies with composer.
   ```
   composer install --optimize-autoloader
   ```

Et voila! You probably want to install the API-side too but the website itself should now be accessible from your configured Vegvisir host.

## API (Reflect)
The API (and database) is where most content is stored and served from on this website.

1. **Download this repo**

   **You can skip this if you've already downloaded the repo from step 1 in the website installation.**

   Otherwise... Git clone or download this repo to any local folder
   ```
   git clone https://github.com/VictorWesterlund/vlw.se
   ```

2. **Download and install Reflect**
   
   Follow the installation instructions for [Reflect](https://github.com/victorwesterlund/vegvisir) and point the `endpoints` variable to the `/api` subdirectory in the local vlw.se folder.

3. **Install dependencies**

   Install dependencies with composer.
   ```
   composer install --optimize-autoloader
   ```

4. **Create and import database**

   [Create and] import the two databases associated with vlw.se data and the Reflect API configurations from `.sql` files on the Releases page.

5. **Set environment variables**

   Make a copy of `/api/.env.example.ini` and change the `[vlwdb]` variables with your MariaDB credentials.

   You also have to generate a [GitHub access token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens) if you wish to use the `releases` endpoint.
   [Read more about this endpoint here](#)

6. **Set environment variables for website**

   It's reasonable to assume if you've installed the website from this repo that you'd also want to use the API with it. Start my making a copy of `/.env.example.ini` (root directory) and change the `[api]` variables to point to your API hostname.

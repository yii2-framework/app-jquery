# Installation guide

This guide covers all supported methods to install and run the jQuery Application Template, including local development
with Composer and containerized deployment with Docker.

## System requirements

- [PHP](https://www.php.net/downloads) `8.2` or higher.
- [Composer](https://getcomposer.org/download/) for dependency management.

## Installation

### Method 1: Using [Composer](https://getcomposer.org/download/) (recommended)

```bash
composer create-project --prefer-dist --stability=dev yii2-framework/app-jquery app-jquery
```

Now you should be able to access the application through the following URL, assuming `app-jquery` is the directory
directly under the Web root.

```text
http://localhost/app-jquery/public/
```

### Method 2: Using the GitHub template

Click the **"Use this template"** button on the GitHub repository page to create your own repository from this template.

Then clone and install:

```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo
composer install
```

## Install with Docker

Update your vendor packages:

```bash
docker-compose run --rm php composer update --prefer-dist
```

Run the installation triggers (creating cookie validation code):

```bash
docker-compose run --rm php composer install
```

Start the container:

```bash
docker-compose up -d
```

You can then access the application through the following URL:

```text
http://127.0.0.1:8000
```

**Notes:**

- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `~/.composer-docker/cache` for Composer caches

## Next steps

- ⚙️ [Configuration Reference](configuration.md)
- 🧪 [Testing Guide](testing.md)

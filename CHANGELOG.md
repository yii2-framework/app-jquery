# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.1.0 Under development

- feat: basic application template for `yii2-framework/core` and `yii2-framework/jquery` packages with Codeception, Docker, and Bootstrap5.
- refactor: simplify namespaces from `yii\demo\basic` to `app` and `yii\demo\basic\tests` to `app\tests`.
- chore: rename repository from `demo-basic` to `app-basic` and convert to GitHub template.
- feat: database-backed authentication with SQLite (signup, login, email verification, password reset).
- chore: rename repository from `app-basic` to `app-jquery` and update all references.
- refactor: align folder names and namespaces to Yii2 lowercase convention.
- docs: simplify `README.md`, add badges and feature SVGs, move detailed content into `docs/` guides, add console tests `HelloControllerTest`.
- feat: add User GridView page with jQuery/Pjax filtering, sorting, and pagination via `UserController`.
- feat: add RBAC with `PhpManager`, admin-only access to Users GridView, and default admin user seed migration.

# Tenant Data Structure (`tenant_template`)

> Database: PostgreSQL · Connection: `tenant`  
> Infrastructure tables omitted (cache, sessions, jobs, oauth_*, migrations, permissions, roles).

---

## Users & Teams

### `users`
| Column                                                                   | Type      | Nullable | Notes         |
| ------------------------------------------------------------------------ | --------- | -------- | ------------- |
| id                                                                       | bigint    | NO       | PK            |
| name                                                                     | varchar   | NO       |               |
| email                                                                    | varchar   | NO       | UNIQUE        |
| email_verified_at                                                        | timestamp | YES      |               |
| workos_id                                                                | varchar   | NO       | UNIQUE        |
| handle                                                                   | text      | NO       | UNIQUE        |
| first_name                                                               | text      | NO       |               |
| middle_name                                                              | text      | YES      |               |
| last_name                                                                | text      | NO       |               |
| title                                                                    | text      | YES      |               |
| avatar                                                                   | text      | NO       |               |
| bio                                                                      | text      | YES      |               |
| phone_main / phone_secondary                                             | text      | YES      |               |
| facebook / x_twitter / instagram / linkedin / youtube / website / github | text      | YES      | Social links  |
| timezone                                                                 | varchar   | YES      | default `UTC` |
| role_data                                                                | jsonb     | YES      |               |
| dashboard_preferences                                                    | jsonb     | YES      |               |
| preferences                                                              | jsonb     | YES      |               |
| n8n_config                                                               | jsonb     | YES      |               |
| cli_config                                                               | jsonb     | YES      |               |
| current_team_id                                                          | bigint    | YES      | FK → teams    |
| remember_token                                                           | varchar   | YES      |               |
| created_at / updated_at                                                  | timestamp | YES      |               |

### `teams`
| Column                  | Type      | Nullable | Notes           |
| ----------------------- | --------- | -------- | --------------- |
| id                      | bigint    | NO       | PK              |
| name                    | varchar   | NO       |                 |
| slug                    | varchar   | NO       | UNIQUE          |
| is_personal             | boolean   | NO       | default `false` |
| deleted_at              | timestamp | YES      | Soft delete     |
| created_at / updated_at | timestamp | YES      |                 |

### `team_members`
| Column                  | Type      | Nullable | Notes                            |
| ----------------------- | --------- | -------- | -------------------------------- |
| id                      | bigint    | NO       | PK                               |
| team_id                 | bigint    | NO       | FK → teams (UNIQUE with user_id) |
| user_id                 | bigint    | NO       | FK → users (UNIQUE with team_id) |
| role                    | varchar   | NO       |                                  |
| created_at / updated_at | timestamp | YES      |                                  |

### `team_invitations`
| Column                  | Type      | Nullable | Notes      |
| ----------------------- | --------- | -------- | ---------- |
| id                      | bigint    | NO       | PK         |
| code                    | varchar   | NO       | UNIQUE     |
| team_id                 | bigint    | NO       | FK → teams |
| email                   | varchar   | NO       |            |
| role                    | varchar   | NO       |            |
| invited_by              | bigint    | NO       | FK → users |
| expires_at              | timestamp | YES      |            |
| accepted_at             | timestamp | YES      |            |
| created_at / updated_at | timestamp | YES      |            |

---

## Organizations

### `organizations`
| Column                  | Type      | Nullable | Notes |
| ----------------------- | --------- | -------- | ----- |
| id                      | bigint    | NO       | PK    |
| user_id                 | bigint    | YES      | Owner |
| name                    | text      | NO       |       |
| description             | text      | YES      |       |
| type                    | varchar   | NO       |       |
| icon                    | text      | YES      |       |
| metadata                | jsonb     | YES      |       |
| created_at / updated_at | timestamp | YES      |       |

### `organization_user`
| Column                  | Type      | Nullable | Notes              |
| ----------------------- | --------- | -------- | ------------------ |
| id                      | bigint    | NO       | PK                 |
| user_id                 | bigint    | NO       | FK → users         |
| organization_id         | bigint    | NO       | FK → organizations |
| elevated                | boolean   | NO       | default `false`    |
| created_at / updated_at | timestamp | YES      |                    |

### `industries`
| Column                  | Type      | Nullable | Notes |
| ----------------------- | --------- | -------- | ----- |
| id                      | bigint    | NO       | PK    |
| name                    | text      | NO       |       |
| description             | text      | YES      |       |
| icon                    | text      | YES      |       |
| created_at / updated_at | timestamp | YES      |       |

---

## Projects & Tasks

### `projects`
| Column                         | Type      | Nullable | Notes              |
| ------------------------------ | --------- | -------- | ------------------ |
| id                             | bigint    | NO       | PK                 |
| organization_id                | bigint    | YES      | FK → organizations |
| name                           | text      | NO       |                    |
| description                    | text      | YES      |                    |
| type                           | varchar   | NO       |                    |
| status                         | varchar   | NO       |                    |
| start_date / end_date          | timestamp | YES      |                    |
| location                       | text      | YES      |                    |
| icon                           | text      | YES      |                    |
| default_break_duration_seconds | text      | YES      |                    |
| metadata                       | jsonb     | YES      |                    |
| deleted_at                     | timestamp | YES      | Soft delete        |
| created_at / updated_at        | timestamp | YES      |                    |

### `project_user`
| Column                  | Type      | Nullable | Notes                               |
| ----------------------- | --------- | -------- | ----------------------------------- |
| id                      | bigint    | NO       | PK                                  |
| project_id              | bigint    | NO       | FK → projects (UNIQUE with user_id) |
| user_id                 | bigint    | NO       | FK → users (UNIQUE with project_id) |
| roles                   | json      | NO       | default `[]`                        |
| created_at / updated_at | timestamp | YES      |                                     |

### `tasks`
| Column                  | Type      | Nullable | Notes           |
| ----------------------- | --------- | -------- | --------------- |
| id                      | bigint    | NO       | PK              |
| project_id              | bigint    | NO       | FK → projects   |
| name                    | varchar   | NO       |                 |
| description             | text      | YES      |                 |
| priority                | varchar   | NO       | default `0`     |
| completed               | boolean   | NO       | default `false` |
| created_at / updated_at | timestamp | YES      |                 |

---

## Time Tracking

### `daily_logs`
| Column                  | Type      | Nullable | Notes         |
| ----------------------- | --------- | -------- | ------------- |
| id                      | bigint    | NO       | PK            |
| user_id                 | bigint    | NO       | FK → users    |
| project_id              | bigint    | NO       | FK → projects |
| date                    | date      | NO       |               |
| total_seconds           | integer   | NO       | default `0`   |
| deleted_at              | timestamp | YES      | Soft delete   |
| created_at / updated_at | timestamp | YES      |               |

### `clock_entries`
| Column                  | Type        | Nullable | Notes                |
| ----------------------- | ----------- | -------- | -------------------- |
| id                      | bigint      | NO       | PK                   |
| daily_log_id            | bigint      | YES      | FK → daily_logs      |
| rate_id                 | bigint      | YES      | FK → rates           |
| in                      | timestamptz | YES      | Clock-in time        |
| out                     | timestamptz | YES      | Clock-out time       |
| timezone                | varchar     | YES      |                      |
| duration_seconds        | integer     | YES      |                      |
| applied_rate            | numeric     | YES      |                      |
| amount                  | numeric     | YES      |                      |
| currency                | varchar     | NO       | default `JPY`        |
| client_id               | uuid        | YES      | UNIQUE (idempotency) |
| notes                   | text        | YES      |                      |
| created_at / updated_at | timestamp   | YES      |                      |

### `activities`
| Column                  | Type      | Nullable | Notes                |
| ----------------------- | --------- | -------- | -------------------- |
| id                      | bigint    | NO       | PK                   |
| user_id                 | bigint    | NO       | FK → users           |
| project_id              | bigint    | NO       | FK → projects        |
| task_category_id        | bigint    | NO       | FK → task_categories |
| date                    | date      | NO       |                      |
| duration                | integer   | NO       | default `0`          |
| notes                   | text      | YES      |                      |
| created_at / updated_at | timestamp | YES      |                      |

### `activity_logs`
| Column                  | Type      | Nullable | Notes               |
| ----------------------- | --------- | -------- | ------------------- |
| id                      | bigint    | NO       | PK                  |
| daily_log_id            | bigint    | NO       | FK → daily_logs     |
| activity_type_id        | bigint    | YES      | FK → activity_types |
| task_id                 | bigint    | YES      | FK → tasks          |
| expense_id              | bigint    | YES      | FK → expenses       |
| start_offset_seconds    | integer   | YES      |                     |
| end_offset_seconds      | integer   | YES      |                     |
| duration_seconds        | integer   | YES      |                     |
| notes                   | text      | YES      |                     |
| created_at / updated_at | timestamp | YES      |                     |

### `activity_types`
| Column                  | Type      | Nullable | Notes |
| ----------------------- | --------- | -------- | ----- |
| id                      | bigint    | NO       | PK    |
| name                    | varchar   | NO       |       |
| description             | varchar   | YES      |       |
| color                   | varchar   | YES      |       |
| icon                    | varchar   | YES      |       |
| created_at / updated_at | timestamp | YES      |       |

### `activity_type_aliases`
| Column                  | Type      | Nullable | Notes               |
| ----------------------- | --------- | -------- | ------------------- |
| id                      | bigint    | NO       | PK                  |
| activity_type_id        | bigint    | NO       | FK → activity_types |
| organization_id         | bigint    | YES      |                     |
| user_id                 | bigint    | YES      |                     |
| alias                   | varchar   | NO       |                     |
| created_at / updated_at | timestamp | YES      |                     |

### `monitored_activities`
| Column                  | Type      | Nullable | Notes |
| ----------------------- | --------- | -------- | ----- |
| id                      | bigint    | NO       | PK    |
| daily_log_id            | bigint    | NO       |       |
| process                 | varchar   | NO       |       |
| window_title            | varchar   | YES      |       |
| timestamp               | timestamp | NO       |       |
| created_at / updated_at | timestamp | YES      |       |

---

## Finance

### `budgets`
| Column                   | Type      | Nullable | Notes           |
| ------------------------ | --------- | -------- | --------------- |
| id                       | bigint    | NO       | PK              |
| project_id               | bigint    | NO       | FK → projects   |
| amount                   | numeric   | NO       |                 |
| amount_low / amount_high | numeric   | YES      | Range estimates |
| currency                 | varchar   | NO       | default `JPY`   |
| type                     | varchar   | NO       | default `fixed` |
| status                   | varchar   | NO       | default `draft` |
| allocated_hours          | integer   | YES      |                 |
| start_date               | timestamp | NO       |                 |
| end_date                 | timestamp | YES      |                 |
| created_at / updated_at  | timestamp | YES      |                 |

### `budget_adjustments`
| Column                                         | Type      | Nullable | Notes         |
| ---------------------------------------------- | --------- | -------- | ------------- |
| id                                             | bigint    | NO       | PK            |
| budget_id                                      | bigint    | NO       | FK → budgets  |
| user_id                                        | bigint    | NO       | FK → users    |
| adjustment_amount                              | numeric   | NO       |               |
| adjustment_amount_low / adjustment_amount_high | numeric   | YES      |               |
| currency                                       | varchar   | NO       | default `JPY` |
| reason                                         | text      | YES      |               |
| created_at / updated_at                        | timestamp | YES      |               |

### `expenses`
| Column                  | Type      | Nullable | Notes           |
| ----------------------- | --------- | -------- | --------------- |
| id                      | bigint    | NO       | PK              |
| project_id              | bigint    | NO       | FK → projects   |
| budget_id               | bigint    | NO       | FK → budgets    |
| user_id                 | bigint    | NO       | FK → users      |
| amount                  | numeric   | NO       |                 |
| currency                | varchar   | NO       | default `JPY`   |
| description             | varchar   | YES      |                 |
| status                  | varchar   | NO       | default `draft` |
| expense_date            | date      | NO       |                 |
| created_at / updated_at | timestamp | YES      |                 |

### `rates`
| Column                           | Type      | Nullable | Notes              |
| -------------------------------- | --------- | -------- | ------------------ |
| id                               | bigint    | NO       | PK                 |
| rate_type_id                     | bigint    | YES      | FK → rate_types    |
| organization_id                  | bigint    | YES      | FK → organizations |
| project_id                       | bigint    | YES      | FK → projects      |
| user_id                          | bigint    | YES      | FK → users         |
| rate_frequency                   | varchar   | NO       | default `hourly`   |
| rate_type                        | varchar   | YES      |                    |
| amount                           | numeric   | NO       |                    |
| currency                         | varchar   | NO       | default `JPY`      |
| overtime_multiplier              | numeric   | NO       | default `1.25`     |
| holiday_multiplier               | numeric   | NO       | default `1.35`     |
| special_multiplier               | numeric   | NO       | default `1.5`      |
| custom_multiplier_rate           | numeric   | YES      |                    |
| custom_multiplier_label          | text      | YES      |                    |
| is_default                       | boolean   | NO       | default `false`    |
| effective_from / effective_until | timestamp | YES      |                    |
| created_at / updated_at          | timestamp | YES      |                    |

### `rate_types`
| Column                  | Type      | Nullable | Notes                  |
| ----------------------- | --------- | -------- | ---------------------- |
| id                      | bigint    | NO       | PK                     |
| name                    | varchar   | NO       |                        |
| description             | text      | YES      |                        |
| scope                   | varchar   | NO       | default `organization` |
| organization_id         | bigint    | YES      | FK → organizations     |
| project_id              | bigint    | YES      | FK → projects          |
| user_id                 | bigint    | YES      | FK → users             |
| created_at / updated_at | timestamp | YES      |                        |

### `currencies`
| Column                  | Type      | Nullable | Notes           |
| ----------------------- | --------- | -------- | --------------- |
| id                      | bigint    | NO       | PK              |
| code                    | varchar   | NO       | UNIQUE          |
| name                    | varchar   | NO       |                 |
| symbol                  | varchar   | NO       |                 |
| symbol_first            | boolean   | NO       | default `true`  |
| is_default              | boolean   | NO       | default `false` |
| exchange_rate           | numeric   | NO       |                 |
| created_at / updated_at | timestamp | YES      |                 |

---

## Git & Repositories

### `repositories`
| Column                  | Type      | Nullable | Notes                   |
| ----------------------- | --------- | -------- | ----------------------- |
| id                      | bigint    | NO       | PK                      |
| user_id                 | bigint    | NO       | FK → users              |
| github_connection_id    | bigint    | YES      | FK → github_connections |
| project_id              | bigint    | YES      | FK → projects           |
| name                    | varchar   | NO       |                         |
| url                     | varchar   | NO       | UNIQUE                  |
| path                    | varchar   | YES      | UNIQUE                  |
| created_at / updated_at | timestamp | YES      |                         |

### `repository_settings`
| Column                   | Type      | Nullable | Notes             |
| ------------------------ | --------- | -------- | ----------------- |
| id                       | bigint    | NO       | PK                |
| repository_id            | bigint    | NO       | FK → repositories |
| branch_id                | bigint    | YES      | FK → branches     |
| excluded_folders         | jsonb     | YES      | default `[]`      |
| excluded_file_extensions | jsonb     | YES      | default `[]`      |
| created_at / updated_at  | timestamp | YES      |                   |

### `branches`
| Column                  | Type      | Nullable | Notes             |
| ----------------------- | --------- | -------- | ----------------- |
| id                      | bigint    | NO       | PK                |
| repository_id           | bigint    | NO       | FK → repositories |
| name                    | varchar   | NO       |                   |
| is_default              | boolean   | NO       | default `false`   |
| created_at / updated_at | timestamp | YES      |                   |

### `git_logs`
| Column                     | Type      | Nullable | Notes             |
| -------------------------- | --------- | -------- | ----------------- |
| id                         | bigint    | NO       | PK                |
| repository_id              | bigint    | NO       | FK → repositories |
| user_id                    | bigint    | NO       |                   |
| daily_log_id               | bigint    | NO       |                   |
| commit_hash                | varchar   | YES      | UNIQUE            |
| author_name / author_email | varchar   | YES      |                   |
| committed_at               | timestamp | YES      |                   |
| message                    | text      | NO       |                   |
| diff                       | text      | YES      |                   |
| created_at / updated_at    | timestamp | YES      |                   |

### `github_connections`
| Column                       | Type      | Nullable | Notes      |
| ---------------------------- | --------- | -------- | ---------- |
| id                           | bigint    | NO       | PK         |
| user_id                      | bigint    | NO       | FK → users |
| github_user_id               | varchar   | NO       |            |
| username                     | varchar   | NO       | UNIQUE     |
| account_handle               | varchar   | YES      |            |
| avatar_url                   | varchar   | YES      |            |
| access_token / refresh_token | varchar   | YES      |            |
| token_expires_at             | timestamp | YES      |            |
| created_at / updated_at      | timestamp | YES      |            |

### `google_connections`
| Column                       | Type      | Nullable | Notes      |
| ---------------------------- | --------- | -------- | ---------- |
| id                           | bigint    | NO       | PK         |
| user_id                      | bigint    | NO       | FK → users |
| google_user_id               | varchar   | NO       | UNIQUE     |
| email                        | varchar   | NO       |            |
| access_token / refresh_token | varchar   | YES      |            |
| token_expires_at             | timestamp | YES      |            |
| created_at / updated_at      | timestamp | YES      |            |

---

## Categorization & Tagging

### `task_categories`
| Column                  | Type      | Nullable | Notes |
| ----------------------- | --------- | -------- | ----- |
| id                      | bigint    | NO       | PK    |
| name                    | varchar   | NO       |       |
| description             | varchar   | YES      |       |
| created_at / updated_at | timestamp | YES      |       |

### `task_category_aliases`
| Column                  | Type      | Nullable | Notes                |
| ----------------------- | --------- | -------- | -------------------- |
| id                      | bigint    | NO       | PK                   |
| task_category_id        | bigint    | NO       | FK → task_categories |
| organization_id         | bigint    | YES      |                      |
| user_id                 | bigint    | YES      |                      |
| alias                   | varchar   | NO       |                      |
| created_at / updated_at | timestamp | YES      |                      |

### `tags`
| Column | Type    | Nullable | Notes |
| ------ | ------- | -------- | ----- |
| id     | bigint  | NO       | PK    |
| label  | varchar | NO       |       |

### `taggable`
Polymorphic pivot — attaches tags to any model.

| Column                  | Type      | Nullable | Notes            |
| ----------------------- | --------- | -------- | ---------------- |
| id                      | bigint    | NO       | PK               |
| taggable_type           | varchar   | NO       | Morph class name |
| taggable_id             | bigint    | NO       | Morph ID         |
| created_at / updated_at | timestamp | YES      |                  |

---

## Reports

### `reports`
| Column                  | Type      | Nullable | Notes                |
| ----------------------- | --------- | -------- | -------------------- |
| id                      | bigint    | NO       | PK                   |
| user_id                 | bigint    | NO       | FK → users           |
| project_id              | bigint    | YES      | FK → projects        |
| organization_id         | bigint    | YES      | FK → organizations   |
| repository_id           | bigint    | YES      | FK → repositories    |
| branch_id               | bigint    | YES      | FK → branches        |
| title                   | varchar   | YES      |                      |
| content                 | text      | YES      |                      |
| report_type             | varchar   | NO       | default `task_based` |
| original_log            | text      | YES      |                      |
| aggregated_diff         | text      | YES      |                      |
| created_at / updated_at | timestamp | YES      |                      |

---

## Voice Commands

### `voice_commands`
| Column                  | Type      | Nullable | Notes      |
| ----------------------- | --------- | -------- | ---------- |
| id                      | bigint    | NO       | PK         |
| user_id                 | bigint    | NO       | FK → users |
| transcript              | text      | NO       |            |
| parsed_command          | jsonb     | YES      |            |
| metadata                | json      | YES      |            |
| created_at / updated_at | timestamp | YES      |            |

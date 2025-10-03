# Runbook (Deploy & Ops)

## Deploy (cPanel)
1) Upload `public_html/*` to hosting `public_html/`.
2) Copy `app/config.sample.php` → `app/config.php` and set secrets server-side.
3) Import `docs/schema.sql` in phpMyAdmin.

## Cron jobs
- Inventory check (hourly):
  `0 * * * * /usr/local/bin/php /home/CPANELUSER/public_html/cron/inventory_check.php`
- Jira Done poller (hourly):
  `15 * * * * /usr/local/bin/php /home/CPANELUSER/public_html/cron/jira_done_poller.php`

## Backups
- Daily MySQL dump via cPanel.
- Weekly archive of `public_html/`.

## Recovery
1) Restore DB dump.
2) Reupload `public_html/`.
3) Recreate `config.php`.

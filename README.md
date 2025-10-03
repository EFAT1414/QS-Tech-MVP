# QS Tech MVP (cPanel PHP/MySQL + Jira + Confluence)
A lightweight MVP that automates the loop: Lead → Jira → Inventory Alerts → KPI Dashboard → Completion Email.

## Quick Start (cPanel)
1) Create MySQL DB + user.
2) Upload `public_html/` contents to your hosting `public_html/`.
3) Copy `public_html/app/config.sample.php` to `public_html/app/config.php` and fill values.
4) Import `docs/schema.sql` in phpMyAdmin.
5) Visit `/modules/lead/create.php` to submit a test lead.
6) Cron jobs:
   - `0 * * * * /usr/local/bin/php /home/USER/public_html/cron/inventory_check.php`
   - `15 * * * * /usr/local/bin/php /home/USER/public_html/cron/jira_done_poller.php`

## Structure
public_html/
  index.php
  app/ (config.sample.php, db.php, jira.php, mailer.php, utils.php)
  modules/
    lead/create.php
    inventory/list.php, po.php
    dashboard/kpis.php
  cron/
    inventory_check.php
    jira_done_poller.php
docs/schema.sql

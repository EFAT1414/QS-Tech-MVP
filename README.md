# QS Tech MVP (cPanel PHP/MySQL + Jira + Confluence)

Repo: https://github.com/EFAT1414/QS-Tech-MVP  
Jira Board: <https://scu-it.atlassian.net/jira/software/c/projects/MSD425PER3/summary>  
Confluence Space: <https://scu-it.atlassian.net/wiki/spaces/MSD425PER3/overview?homepageId=331776439>

## What this does
Lead → Jira issue → Inventory low-stock alert + draft PO → KPI dashboard → Completion email on Jira Done.

## Quick demo
1) Open `/modules/lead/create.php`, submit a lead.
2) Jira issue auto-creates (if JIRA_* configured).
3) Add inventory item qty<threshold → low-stock highlight → Draft PO.
4) Cron sends PM alert (hourly) and completion emails for Jira Done.


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

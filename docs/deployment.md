# Deploying to sallaamti.com (cPanel)

One-time setup so private repos can be pulled without hitting GitHub's 2FA
prompt, and so a push to `main` deploys to the live site automatically —
no more manual cPanel Git pulls, and no more repos left public just to
dodge auth.

Do Part 1 first (fixes today's problem: repos can go back to private).
Part 2 is optional but recommended (removes the manual-pull step entirely).
Repeat both parts for each of your other projects on this same cPanel
account — one token covers all of them, but each project needs its own
webhook secret, deploy script, and cron entry.

## Part 1 — one token, works for every repo, no more public repos

**1. Create a fine-grained Personal Access Token**
GitHub -> your avatar -> Settings -> Developer settings -> Personal access
tokens -> Fine-grained tokens -> Generate new token.
- Name: `cpanel-deploy`
- Expiration: max allowed (you'll get an email reminder before it expires —
  when that happens, generate a new one and repeat step 3 below)
- Repository access: "Only select repositories" -> pick every repo you
  deploy from this cPanel account
- Permissions -> Repository permissions -> **Contents: Read-only** (that's
  the only permission `git pull` needs)
- Generate, and copy the token now — GitHub only shows it once.

**2. Store it once on the server, not per-repo**
In cPanel Terminal:
```bash
git config --global credential.helper store
echo "https://<YOUR-GITHUB-USERNAME>:<TOKEN>@github.com" > ~/.git-credentials
chmod 600 ~/.git-credentials
```
`~/` here is your account's home directory, which cPanel never serves over
the web (only `public_html`/your app subfolders are web-accessible), so
this file is safe there.

**3. Point each repo's remote at plain HTTPS** (the credential helper only
kicks in for HTTPS remotes, not SSH ones)
```bash
cd ~/path-to-sallaamti
git remote set-url origin https://github.com/<org-or-user>/<repo>.git
git pull origin main
```
It should pull with no password/2FA prompt. Repeat for each project's
subfolder.

**4. Make the repos private again**
GitHub -> each repo -> Settings -> Danger Zone -> Change visibility ->
Private. Pulling no longer depends on the repo being public.

## Part 2 — push to `main` deploys automatically

This app now has a webhook receiver at `POST /webhooks/github-deploy`
(`app/Http/Controllers/DeployWebhookController.php`). It does **not** run
git or composer itself — a web request on shared hosting isn't reliable
enough to survive a full deploy (and GitHub only waits ~10s for a
response before marking the delivery failed). It just verifies the
request really came from GitHub and drops a flag file; a cron job checks
for that flag once a minute and does the actual pull.

**1. Generate a webhook secret and add it to production `.env`**
```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```
Add the output to `~/path-to-sallaamti/.env`:
```
DEPLOY_WEBHOOK_SECRET=<paste-it-here>
```

**2. Create the deploy script** — `~/deploy-sallaamti.sh` (adjust the path
on the `cd` line to your real app directory):
```bash
#!/bin/bash
LOCK=~/deploy-sallaamti.lock
[ -f "$LOCK" ] && exit 0
touch "$LOCK"

cd ~/path-to-sallaamti || { rm -f "$LOCK"; exit 1; }

{
  echo "=== Deploy $(date) ==="
  git pull origin main
  composer install --no-dev --optimize-autoloader --no-interaction
  php artisan optimize:clear
  php artisan view:cache
  php artisan config:cache
} >> ~/deploy-sallaamti.log 2>&1

rm -f "$LOCK"
```
This deliberately does **not** run `php artisan migrate` automatically —
an unattended migration on every push is one bad migration away from
taking the live site down with nobody watching. Run migrations by hand
when a change actually needs one.
```bash
chmod +x ~/deploy-sallaamti.sh
```

**3. Create the cron-trigger script** — `~/check-deploy-sallaamti.sh`:
```bash
#!/bin/bash
FLAG=~/path-to-sallaamti/storage/app/deploy.flag
if [ -f "$FLAG" ]; then
    rm -f "$FLAG"
    ~/deploy-sallaamti.sh
fi
```
```bash
chmod +x ~/check-deploy-sallaamti.sh
```

**4. Add the cron job** — cPanel -> Cron Jobs -> Add New Cron Job:
- Common Settings: **Once Per Minute** (`* * * * *`)
- Command: `~/check-deploy-sallaamti.sh`

(If your host's cPanel won't allow a 1-minute interval, 5 minutes is the
usual fallback — deploys just take up to 5 minutes to land instead of up
to 1.)

**5. Add the GitHub webhook** — repo -> Settings -> Webhooks -> Add
webhook:
- Payload URL: `https://sallaamti.com/webhooks/github-deploy`
- Content type: `application/json`
- Secret: same value as `DEPLOY_WEBHOOK_SECRET`
- Which events: "Just the push event"
- Active: checked

**6. Test it** — push a trivial commit to `main` and watch
`~/deploy-sallaamti.log` fill in within a minute. GitHub also shows the
delivery result under the webhook's "Recent Deliveries" tab if something
doesn't fire.

## Repeating for another project on the same cPanel account

The token from Part 1 already covers any repo you selected when creating
it (edit the token's repository access list on GitHub to add more later).
For auto-deploy, each project needs its own copy of steps 1-6 in Part 2 —
its own `DEPLOY_WEBHOOK_SECRET`, its own `deploy-<project>.sh` /
`check-deploy-<project>.sh` pair, its own cron entry, and its own webhook
pointing at that project's domain.

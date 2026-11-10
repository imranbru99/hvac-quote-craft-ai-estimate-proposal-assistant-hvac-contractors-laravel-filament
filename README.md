# HvacQuoteCraft — AI Estimate & Proposal Assistant for HVAC Contractors | Laravel Filament SaaS

**HvacQuoteCraft** is a world-class AI estimate and proposal assistant built for **HVAC contractors**, operations leads, and revenue teams in home & field services. Draft itemized quotes from intake details, generate customer proposals, require human approval, and keep a full audit trail — with modular features you can turn on or off for your shop.

**Local demo:** http://localhost:8109 · **Admin:** http://localhost:8109/admin

## Why HVAC contractors choose HvacQuoteCraft

HVAC owners lose deals when estimates sit in spreadsheets. HvacQuoteCraft automates the revenue-operations loop:

1. **Intake** — service requests for installs, replacements, heat pumps, rooftop units, and maintenance bids  
2. **AI draft** — itemized line items, tax, and totals from equipment + notes  
3. **Human approval** — owner / estimator maker-checker gate  
4. **Proposal + audit** — customer-facing proposal packs, consent-aware follow-ups, immutable audit log  

Target buyer: HVAC Contractors owner / operations lead · Opportunity score 99 · Pilot pricing $299–$1,499/mo  
Integrations ready: Jobber / ServiceTitan / QuickBooks / Google Calendar  

## Core MVP workflow

`1 intake trigger → AI draft estimate → human approval → proposal send → audit log`

Operate on **service requests**, **field technicians**, and **job tickets** alongside estimates and proposals.

## Demo users

All passwords: `password`

| Role | Email |
|------|-------|
| Super Admin | admin@demo.local |
| HVAC Owner | owner@demo.local |
| Operations Lead | manager@demo.local |
| Estimator | estimator@demo.local |
| Office Staff | staff@demo.local |
| Field Technician | tech@demo.local |
| Customer Portal | customer@demo.local |

## Docker quick start

```bash
cd pipeline/apps/HvacQuoteCraft
docker compose up -d --build
```

- App: http://localhost:8109  
- MySQL host port: **33069**  
- Redis host port: **63799**  

Containers: `hvac-quote-craft-app`, `hvac-quote-craft-mysql`, `hvac-quote-craft-redis`

If a container name conflicts: `docker rm -f hvac-quote-craft-app hvac-quote-craft-mysql hvac-quote-craft-redis` then compose up again.

## Feature modules (toggle ON/OFF)

Every module can be enabled or disabled from Admin → **Module Control** (locked core modules stay on):

- Core Platform, Users & RBAC  
- Estimate Dashboard  
- Service Requests, Job Tickets, Field Technicians  
- **Estimates & Proposals** (core)  
- **AI Quote Drafter** (core)  
- Proposal Follow-ups  
- CRM & Pipeline, Parts Inventory  
- Purchasing & Suppliers *(off)* · Sales & Counter POS *(off)*  
- Billing & Collections *(off by default)*  
- Double-Entry Accounting  
- HR & Payroll *(off)* · Biometric Attendance Hub *(off)*  
- Scheduling & Calendar · Jobber / ServiceTitan / QuickBooks hooks  
- Documents & Unicode PDF · AI Hub Assistant · AI Catalog Ingest *(off)*  
- Customer Portal *(off)* · SMS / WhatsApp / Email  
- Estimate & Win-Rate Reports · Headless REST API  
- Drutogoti Profiler · Media Studio · Audit & Approval Log  
- Internal Helpdesk *(off)* · Quality & Safety · Production *(off)* · Logistics *(off)*  

## Tech stack

- PHP 8.4 · Laravel 12 · Filament 3  
- MySQL 8.4 · Redis 7  
- Spatie Laravel Permission (multi-role)  
- Docker Compose (Apache + intl extension for Filament)  
- Optional Packagist modules: `imrandevbd/drutogoti`, `laravel-admin-panel`, `laravel-ai-catalog`, `laravel-ai-hub`, `laravel-attendance-hub`, `laravel-unicode-pdf`, `laravel-universal-slug` (dev-main / prefer-stable). If Packagist install fails, local equivalents ship under `app/Services` and Filament resources.

## Screenshots

See `docs/screenshots/` after smoke test:

- Home / marketing  
- Features & modules  
- Admin dashboard  
- Estimates module  

Public marketing pages: `/` `/features` `/modules` `/pricing` `/demo` `/docs` `/screens` `/contact`

## SEO / related keywords

HVAC estimate software, HVAC proposal software, HVAC quote software, AI estimate assistant, AI proposal generator, HVAC contractor CRM, HVAC quoting tool, itemized HVAC estimate, heat pump proposal, rooftop unit quote, residential HVAC estimate, commercial HVAC bid, ServiceTitan alternative quoting, Jobber estimate automation, QuickBooks HVAC estimate, Google Calendar HVAC scheduling, field service estimate software, HVAC revenue operations, HVAC ops lead software, HVAC owner dashboard, technician job tickets, service request intake, Laravel Filament HVAC SaaS, modular HVAC ERP, AI business automation HVAC, HVAC contractor SaaS 2026, duct sealing estimate, SEER heat pump quote, EPA compliance HVAC proposal, maker-checker estimate approval, HVAC audit log, messaging consent HVAC follow-up, HVAC win rate reporting, multi-branch HVAC estimating, Dallas HVAC software, USA HVAC contractors automation, home and field services software, AI draft quote HVAC, human approval workflow, Laravel Filament admin panel, Docker HVAC SaaS, imrandevbd packages, Bengali Unicode PDF estimates, universal slug HVAC catalog, attendance hub field crew, AI catalog price sheets HVAC

## Author

Built by **Imran Ahmed (IMRAN Dev BD)** — https://imrandev.bd · me@imrandev.bd

## Contact / Hire

- Developer: Imran Ahmed (IMRAN Dev BD)
- Website: https://imrandev.bd/contact
- Email: me@imrandev.bd
- GitHub: https://github.com/imranbru99

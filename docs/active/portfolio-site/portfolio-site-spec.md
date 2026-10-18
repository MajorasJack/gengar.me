# gengar.me Portfolio - Spec

Last Updated: 2026-10-18

This file covers what we are building and why. How is in `portfolio-site-plan.md`; the work is in `portfolio-site-tasks.md`.

## 1. Problem
Jack Evans is a senior PHP/Laravel engineer in Manchester, available for work since June 2026. Recruiters and hiring managers who search for him find a LinkedIn profile and a CyberPanel placeholder page at `gengar.me`. Five deployed personal projects (Promotr, Larder, Darkroom, DrinkSafe, Movies) show more about how he works than a CV can, but nothing links them together. The inventory is in `research/01-project-inventory.md`.

## 2. Goals
| ID | Goal | How we will know |
|---|---|---|
| G1 | A visitor can tell who Jack is, what he does and how to contact him within one screen | Name, role, availability, email and CV link are visible above the fold at 1280x800 and 390x844 |
| G2 | The projects act as evidence of backend skill | Each featured project shows its stack, test count and where it runs; each has a detail page |
| G3 | The site's own repo is a credible code sample | Public repo, CI green on Pest, Pint and Larastan |
| G4 | It stays cheap to run and keep up to date | No database; adding a project is one config entry; one-command deploy |

## 3. Principles
1. Quiet over clever. No animation, no JavaScript framework, nothing a backend engineer would have to maintain blind.
2. Public bio rules. No past employer names, no raw performance figures; describe the method and the industries instead. The CV download carries the specifics.
3. Evidence over adjectives. Show the stack, the tests and the live URL instead of describing quality.
4. Honest about the front end. The copy says that front ends are built AI-first with Claude Code and that Jack owns the architecture, data model, tests and review.
5. Nothing on the page that a scraper can abuse. No phone number, no form, no cookies.

## 4. Users
| Persona | Description | Main needs |
|---|---|---|
| Recruiter | Non-technical, skimming several candidates | Role, location, availability, CV, email |
| Hiring manager / engineering lead | Technical, judging depth | What he has built, stack, tests, code on GitHub |
| Jack (owner) | Maintains the site between job hunts | Edit content in one file, toggle availability, deploy in one command |

## 5. Scope (MoSCoW)
**Must**
- Homepage in design direction C (dark "server list") with intro, what I do, projects table, contact and CV link
- Availability flag in config
- Deployed to `gengar.me` on the existing CyberPanel server

**Should**
- A detail page per featured project
- "Smaller tools" list
- Light theme that follows the system setting
- Open Graph image, sitemap, robots.txt

**Could**
- `/uses` page listing tools and setup
- Screenshots on detail pages where assets exist (Promotr, DrinkSafe)

**Won't**
- Blog or CMS
- Contact form
- Analytics or tracking
- Live uptime polling of the projects (status comes from config)
- Admin panel

## 6. Non-goals
- Showcasing front-end skill through visual effects. The audience is hiring for backend work.
- Replacing LinkedIn or the tailored CVs. The site points to the generic CV only.
- Automatically counting tests across project repos. Counts change slowly, so they are kept by hand in config with a "last checked" date.

## 7. Requirements
Notation is EARS. Mockup reference is direction C in the published mockups (see context file).

### 7.1 Content (CONT)
| ID | Pri | Requirement |
|---|---|---|
| CONT-1 | M | WHEN a visitor opens `/` THE SYSTEM SHALL show Jack's name, the role "senior PHP / Laravel engineer", "Manchester, UK", a remote-first note and an intro paragraph of no more than 70 words |
| CONT-2 | M | THE SYSTEM SHALL show a "what I do" section of four areas (performance, APIs and integrations, engineering standards, AI-assisted development with Claude Code), each with a one-sentence description |
| CONT-3 | M | THE SYSTEM SHALL list featured projects from `config/portfolio.php` in config order, each with name, host, one-line description, stack summary, approximate test count and a status dot |
| CONT-4 | M | IF a project has no public URL THEN THE SYSTEM SHALL show its `visibility` label (for example "invite only") in place of the host, with no link |
| CONT-5 | S | THE SYSTEM SHALL show a "smaller tools" list (Invoice Inbox, pre-orders Shopify app, Shopify image optimiser) without links and without naming shops, stores or suppliers |
| CONT-6 | M | THE SYSTEM SHALL NOT render any name on the denylist in `config/portfolio.php` (past employers, private brands) or any performance figure written as `<n>s → <n>s` |
| CONT-7 | S | WHEN a visitor opens `/projects/{slug}` for a featured project THE SYSTEM SHALL show the problem, what was built, three to five engineering notes, the stack and the link or visibility label |
| CONT-8 | M | IF `{slug}` matches no featured project THEN THE SYSTEM SHALL return a HTTP `404` page styled like the site |

### 7.2 Contact (CONTACT)
| ID | Pri | Requirement |
|---|---|---|
| CONTACT-1 | M | THE SYSTEM SHALL show `jack@gengar.me` as literal text with a `mailto:` link, plus links to LinkedIn and GitHub (`MajorasJack`) |
| CONTACT-2 | M | THE SYSTEM SHALL NOT include a phone number in any rendered page |
| CONTACT-3 | M | WHEN a visitor selects "Download CV" THE SYSTEM SHALL serve `/cv.pdf` with `Content-Type: application/pdf` |
| CONTACT-4 | M | WHILE `portfolio.available` is true THE SYSTEM SHALL show "available for work"; WHILE false THE SYSTEM SHALL omit it |

### 7.3 Interface (UI)
| ID | Pri | Requirement |
|---|---|---|
| UI-1 | M | THE SYSTEM SHALL render the dark palette from mockup C by default, using CSS custom properties for every colour |
| UI-2 | S | WHEN the visitor's system prefers a light scheme THE SYSTEM SHALL switch to a light palette with the same accent hue |
| UI-3 | M | WHILE the viewport is narrower than 640px THE SYSTEM SHALL render the projects table as stacked rows, with no horizontal scrolling of the page body |
| UI-4 | M | THE SYSTEM SHALL work fully with JavaScript disabled and ship no JavaScript on public pages |

### 7.4 Operations (OPS)
| ID | Pri | Requirement |
|---|---|---|
| OPS-1 | M | WHEN `deploy/deploy.sh` runs locally THE SYSTEM SHALL build a release tarball (production dependencies and built assets), upload it, unpack it into a new release directory on the server, cache config, routes and views, then switch `current` to the new release atomically |
| OPS-2 | M | IF any deploy step fails THEN THE SYSTEM SHALL leave `current` pointing at the previous release |
| OPS-3 | M | THE SYSTEM SHALL keep the last three releases so a rollback is one symlink change |
| OPS-4 | M | WHEN `/up` is requested THE SYSTEM SHALL return a HTTP `200` status |
| OPS-5 | S | WHEN a request arrives on `www.gengar.me` or over HTTP THE SYSTEM SHALL redirect with a HTTP `301` status to `https://gengar.me` with the same path |

### 7.5 Non-functional (NFR)
| ID | Pri | Requirement |
|---|---|---|
| NFR-1 | M | THE SYSTEM SHALL transfer under 150 KB (HTML, CSS and fonts) for the homepage |
| NFR-2 | M | THE SYSTEM SHALL score at least 95 for performance and 100 for accessibility in Lighthouse (mobile) |
| NFR-3 | M | THE SYSTEM SHALL meet WCAG 2.2 AA contrast in both palettes and show a visible focus state on every link |
| NFR-4 | M | THE SYSTEM SHALL make no third-party requests (fonts self-hosted, no analytics) and set no cookies on public pages |
| NFR-5 | M | THE SYSTEM SHALL keep no secrets in the repository; CI SHALL fail if `.env` or a private key is committed |
| NFR-6 | M | THE SYSTEM SHALL pass Pest, Pint and Larastan (level 8) in CI on every push |
| NFR-7 | S | THE SYSTEM SHALL serve a meta description, an Open Graph image, `sitemap.xml` and `robots.txt` |

## 8. Open questions
| # | Question | Blocks | Default if unanswered |
|---|---|---|---|
| 1 | ~~The generic CV includes the phone number. Should the public `/cv.pdf` drop it?~~ Answered 2026-10-18 | - | Yes. Build `cv/main_portfolio.tex` with the phone number removed |
| 2 | ~~Are `promotr.co` and `drinksafe.app` publicly live?~~ Answered 2026-10-18 | - | Check each in S1-03; link if it returns a HTTP `200`, otherwise label "in development" |
| 3 | ~~Final name for Larder?~~ Answered 2026-10-18 | - | Keep "Larder"; the name lives in one config entry |
| 4 | ~~Should the site include a photo of Jack?~~ Answered 2026-10-18 | - | No photo |
| 5 | ~~Light or dark when the system has no preference?~~ Answered 2026-10-18 | - | Dark (direction C) |
| 6 | ~~Should the pre-orders app link anywhere?~~ Answered 2026-10-18 | - | No link, like the other smaller tools |
| 7 | ~~Which names belong on the CONT-6 denylist?~~ Answered 2026-10-18 | - | Every employer in the CV profile plus Jack's own shop and label brands (stored as hashes, see S1-05) |

## 9. Glossary
| Term | Meaning |
|---|---|
| Direction C | The dark, purple-accented "server list" mockup chosen on 2026-10-18 |
| Featured project | One of the five projects in the main table |
| Smaller tools | Unlinked list of personal tools tied to Jack's own shops |
| Release | A timestamped directory under `app/releases/` on the server |
| Denylist | Names that must not appear on any public page (CONT-6) |

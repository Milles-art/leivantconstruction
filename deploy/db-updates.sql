-- Leivant deploy DB updates — run in phpMyAdmin (database: leivantc_leivant)
-- Safe to run twice: services use INSERT IGNORE, migrations guarded by unique index.

-- 1) New public service pages (fixes 5 URLs that currently 404)
INSERT IGNORE INTO services (name,slug,summary,description,is_active,created_at,updated_at) VALUES
('House Construction','house-construction-tanzania','New residential builds, extensions, and structural renovations delivered and supervised.','Leivant plans, supervises, and delivers residential construction projects in Tanzania: new builds, floor extensions, structural renovations, and finishing work. Our site team manages labour scheduling, quality checks, and progress reporting so clients know exactly where their build stands at every stage.',1,NOW(),NOW()),
('BOQ Preparation','boq-preparation-tanzania','Detailed Bills of Quantities, material schedules, and site-specific cost estimates.','Before any project breaks ground, the numbers need to be right. Leivant prepares detailed Bills of Quantities, material schedules, and site-specific cost estimates, helping clients and developers understand the real cost of their build before committing funds or signing contracts.',1,NOW(),NOW()),
('Equipment Rental','construction-equipment-rental','Excavators, mixers, compactors, and site machinery assessed for access, phase, and duration.','Leivant supplies and coordinates construction machinery for active sites: excavators, concrete mixers, compactors, and supporting equipment. Every rental is assessed against site access conditions, project phase, and duration to avoid downtime and over-cost.',1,NOW(),NOW()),
('Renovation Contractor','renovation-contractor-dar-es-salaam','Structural renovations, extensions, and finishing upgrades across Dar es Salaam.','Leivant renovates and upgrades existing structures in Dar es Salaam: structural repairs, floor extensions, interior and exterior finishing, and supervised handover. One accountable team from assessment through completion.',1,NOW(),NOW()),
('Materials Supply','building-materials-supply','Cement, steel, blocks, roofing, and finishes sourced, scheduled, and delivered to site.','Cement, steel reinforcement, building blocks, roofing sheets, tiles, and finishing materials: sourced, scheduled, and delivered to site. Leivant coordinates materials against the build timeline so work is never held up waiting for stock.',1,NOW(),NOW());

-- 2) Mark the two already-existing mail tables as migrated
INSERT IGNORE INTO migrations (migration,batch) VALUES
('2026_05_23_000600_create_admin_mail_tables',2),
('2026_05_24_000700_full_admin_mail_client',2);

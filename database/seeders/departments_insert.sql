-- SQL INSERT statements for departments table
-- Compatible with the DepartmentSeeder.php structure

-- Option 1: Insert with update on duplicate (recommended - matches seeder behavior)
INSERT INTO departments (department_name, slug, status, created_at, updated_at) VALUES
('Management / BOD', 'management-bod', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Internal Audit & System', 'internal-audit-system', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Corporate Secretary', 'corporate-secretary', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('APS - Arka Project Support', 'aps-arka-project-support', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Relationship & Coordination', 'relationship-coordination', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Design & Construction', 'design-construction', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Finance', 'finance', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Human Capital & Support', 'human-capital-support', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Asset Management & Logistic', 'asset-management-logistic', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Accounting', 'accounting', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Plant', 'plant', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Procurement', 'procurement', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Marketing', 'marketing', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Operation & Production', 'operation-production', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Safety', 'safety', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Information Technology', 'information-technology', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Research & Development', 'research-development', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE 
    department_name = VALUES(department_name),
    status = VALUES(status),
    updated_at = CURRENT_TIMESTAMP;

-- Option 2: Insert ignore duplicates (skips if already exists)
-- INSERT IGNORE INTO departments (department_name, slug, status, created_at, updated_at) VALUES
-- ('Management / BOD', 'management-bod', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Internal Audit & System', 'internal-audit-system', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Corporate Secretary', 'corporate-secretary', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('APS - Arka Project Support', 'aps-arka-project-support', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Relationship & Coordination', 'relationship-coordination', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Design & Construction', 'design-construction', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Finance', 'finance', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Human Capital & Support', 'human-capital-support', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Asset Management & Logistic', 'asset-management-logistic', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Accounting', 'accounting', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Plant', 'plant', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Procurement', 'procurement', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Marketing', 'marketing', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Operation & Production', 'operation-production', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Safety', 'safety', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Information Technology', 'information-technology', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
-- ('Research & Development', 'research-development', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

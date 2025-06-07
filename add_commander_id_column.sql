-- Add commander_id column to units table if it doesn't exist
ALTER TABLE units ADD COLUMN IF NOT EXISTS commander_id INT DEFAULT NULL;

-- Add foreign key constraint
ALTER TABLE units
ADD CONSTRAINT fk_commander
FOREIGN KEY (commander_id) REFERENCES people(id)
ON DELETE SET NULL; 
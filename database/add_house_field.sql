-- Add house field to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS house VARCHAR(50) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS house_logo VARCHAR(255) DEFAULT NULL;

-- Update existing users to have NULL house (they can choose later)


-- Initialize the Guardian Project Management System Database
-- This script sets up the initial database configuration

-- Create additional schemas if needed
CREATE SCHEMA IF NOT EXISTS guardian_system;
CREATE SCHEMA IF NOT EXISTS guardian_audit;

-- Set default search path
ALTER DATABASE guardian_db SET search_path TO public, guardian_system, guardian_audit;

-- Create extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";
CREATE EXTENSION IF NOT EXISTS "pg_stat_statements";

-- Create custom data types
DO $$ BEGIN
    CREATE TYPE environment_type AS ENUM ('production', 'staging', 'development', 'testing');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE user_role_type AS ENUM ('super_admin', 'admin', 'project_manager', 'team_lead', 'team_member', 'observer');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE project_status_type AS ENUM ('planning', 'active', 'on_hold', 'review', 'completed', 'cancelled', 'archived');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE task_status_type AS ENUM ('backlog', 'todo', 'in_progress', 'review', 'testing', 'done', 'cancelled');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE priority_type AS ENUM ('low', 'medium', 'high', 'critical', 'urgent');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- Create audit function for tracking changes
CREATE OR REPLACE FUNCTION guardian_audit.audit_trigger()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        INSERT INTO guardian_audit.audit_log (
            table_name, operation, new_data, user_id, created_at
        ) VALUES (
            TG_TABLE_NAME, TG_OP, row_to_json(NEW), 
            COALESCE(current_setting('guardian.user_id', true)::integer, 0),
            NOW()
        );
        RETURN NEW;
    ELSIF TG_OP = 'UPDATE' THEN
        INSERT INTO guardian_audit.audit_log (
            table_name, operation, old_data, new_data, user_id, created_at
        ) VALUES (
            TG_TABLE_NAME, TG_OP, row_to_json(OLD), row_to_json(NEW),
            COALESCE(current_setting('guardian.user_id', true)::integer, 0),
            NOW()
        );
        RETURN NEW;
    ELSIF TG_OP = 'DELETE' THEN
        INSERT INTO guardian_audit.audit_log (
            table_name, operation, old_data, user_id, created_at
        ) VALUES (
            TG_TABLE_NAME, TG_OP, row_to_json(OLD),
            COALESCE(current_setting('guardian.user_id', true)::integer, 0),
            NOW()
        );
        RETURN OLD;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Grant permissions
GRANT USAGE ON SCHEMA guardian_system TO falcon_user;
GRANT USAGE ON SCHEMA guardian_audit TO falcon_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO falcon_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA guardian_system TO falcon_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA guardian_audit TO falcon_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO falcon_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA guardian_system TO falcon_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA guardian_audit TO falcon_user;

-- Set default privileges for future objects
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO falcon_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA guardian_system GRANT ALL ON TABLES TO falcon_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA guardian_audit GRANT ALL ON TABLES TO falcon_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO falcon_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA guardian_system GRANT ALL ON SEQUENCES TO falcon_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA guardian_audit GRANT ALL ON SEQUENCES TO falcon_user;

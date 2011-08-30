-- ******************************************************************************
-- ************************************prep**************************************
-- ******************************************************************************

DELIMITER $$

DROP PROCEDURE IF EXISTS commonservices_production.merge_prep $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.merge_prep()
BEGIN
    -- Create tracking table
    DROP TABLE IF EXISTS commonservices_production.merge_tracking;
    CREATE TABLE commonservices_production.merge_tracking (
        id INT NOT NULL AUTO_INCREMENT,
        the_table VARCHAR(255) NOT NULL DEFAULT '',
        old_id VARCHAR(255) NOT NULL DEFAULT '',
        new_id VARCHAR(255) NOT NULL DEFAULT '',
        logged_at DATETIME NOT NULL DEFAULT '00:00:00 00-00-0000',
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    -- Create conflicts table
    DROP TABLE IF EXISTS commonservices_production.merge_conflicts;
    CREATE TABLE commonservices_production.merge_conflicts (
        id INT NOT NULL AUTO_INCREMENT,
        the_table VARCHAR(255) NOT NULL DEFAULT '',
        the_field VARCHAR(255) NOT NULL DEFAULT '',
        the_id VARCHAR(255) NOT NULL DEFAULT '',
        logged_at DATETIME NOT NULL DEFAULT '00:00:00 00-00-0000',
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    -- Prepare 'people' table
    ALTER TABLE commonservices_production.people ADD (source_region VARCHAR(255) DEFAULT 'aalto');
    -- End
    SELECT "Merge tables created, 'people' table updated";
END $$


-- ******************************************************************************
-- ************************************people************************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.people_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.people_merge()
BEGIN
    -- Declare variables
    DECLARE var_person_id INT;
    DECLARE conflict_counter INT DEFAULT 0;
    DECLARE resolved_conflicts INT DEFAULT 0;
    DECLARE var_person_email VARCHAR(255);
    DECLARE var_person_username VARCHAR(255);
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the poeple cursor
    DECLARE people_cursor CURSOR FOR SELECT id FROM asi_nairobi.people;
    -- Open people cursor
    OPEN people_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE people_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch person id
        FETCH people_cursor INTO var_person_id;
        -- Check for and log any person conflicts
        SELECT email INTO var_person_email FROM asi_nairobi.people WHERE id = var_person_id;
        SELECT username INTO var_person_username FROM asi_nairobi.people WHERE id = var_person_id;
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.people WHERE username = var_person_username) THEN
            IF NOT EXISTS (SELECT 1 FROM commonservices_production.people WHERE email = var_person_email) THEN
                IF NOT EXISTS (SELECT 1 FROM commonservices_production.people WHERE guid = (SELECT guid FROM asi_nairobi.people WHERE id = var_person_id)) THEN
                    -- No conflicts, insert person
                    INSERT INTO commonservices_production.people (username, encrypted_password, created_at, updated_at, email, salt, consent, coin_amount,
                        is_association, status_message, status_message_changed, gender, irc_nick, msn_nick, phone_number, description, website,
                        birthdate, guid, delta, source_region)
                        SELECT username, encrypted_password, created_at, updated_at, email, salt, consent, coin_amount, is_association, status_message,
                            status_message_changed, gender, irc_nick, msn_nick, phone_number, description, website, birthdate, guid, delta, 'nairobi'
                            FROM asi_nairobi.people WHERE id = var_person_id;
                    -- Tracking
                    INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('people', var_person_id, LAST_INSERT_ID(), NOW());
                ELSE 
                    SET conflict_counter = conflict_counter + 1;
                    INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('people', 'guid', var_person_id, NOW());
                END IF;
            ELSE
                SET conflict_counter = conflict_counter + 1;
                INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('people', 'email', var_person_id, NOW());
                -- Email conflicts resolution
                IF var_person_id != 549 THEN
                    -- The other two, delete their ASI a/cs
                    DELETE FROM commonservices_production.people WHERE email = var_person_email;
                    -- Then copy over their Nairobi Sizzle a/cs
                    INSERT INTO commonservices_production.people (username, encrypted_password, created_at, updated_at, email, salt, consent, coin_amount,
                        is_association, status_message, status_message_changed, gender, irc_nick, msn_nick, phone_number, description, website,
                        birthdate, guid, delta, source_region)
                        SELECT username, encrypted_password, created_at, updated_at, email, salt, consent, coin_amount, is_association, status_message,
                            status_message_changed, gender, irc_nick, msn_nick, phone_number, description, website, birthdate, guid, delta, 'nairobi'
                            FROM asi_nairobi.people WHERE id = var_person_id;
                    -- Tracking
                    INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('people', var_person_id, LAST_INSERT_ID(), NOW());
                    SET resolved_conflicts = resolved_conflicts + 1;
                END IF;
            END IF;
        ELSE
            SET conflict_counter = conflict_counter + 1;
            INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('people', 'username', var_person_id, NOW());
            -- Usernames conflict resolution
            IF EXISTS (SELECT 1 FROM commonservices_production.people WHERE username = CONCAT(var_person_username, '_nairobi')) THEN
                SET var_person_username = CONCAT(var_person_username, '_nairobi1');
            ELSE
                SET var_person_username = CONCAT(var_person_username, '_nairobi');
            END IF;
            INSERT INTO commonservices_production.people (username, encrypted_password, created_at, updated_at, email, salt, consent, coin_amount,
                is_association, status_message, status_message_changed, gender, irc_nick, msn_nick, phone_number, description, website,
                birthdate, guid, delta, source_region)
                SELECT var_person_username, encrypted_password, created_at, updated_at, email, salt, consent, coin_amount, is_association, status_message,
                    status_message_changed, gender, irc_nick, msn_nick, phone_number, description, website, birthdate, guid, delta, 'nairobi'
                    FROM asi_nairobi.people WHERE id = var_person_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('people', var_person_id, LAST_INSERT_ID(), NOW());
            SET resolved_conflicts = resolved_conflicts + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    IF conflict_counter = 0 THEN
        SELECT "People merge completed. No conflicts found";
    ELSE
        SELECT "People merge completed.", conflict_counter, "conflicts found", resolved_conflicts, "conflicts resolved", 1, "ignored";
    END IF;
END $$


-- ******************************************************************************
-- ************************************clients***********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.clients_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.clients_merge()
BEGIN
    -- Declare variables
    DECLARE var_client_id VARCHAR(255);
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the clients cursor
    DECLARE clients_cursor CURSOR FOR SELECT id FROM asi_nairobi.clients;
    -- Open clients cursor
    OPEN clients_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE clients_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch clients id
        FETCH clients_cursor INTO var_client_id;
        -- Check for and log any clients conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.clients WHERE id = var_client_id) THEN
            -- No conflicts, insert client
            INSERT INTO commonservices_production.clients (`id`, `name`, `created_at`, `updated_at`, `encrypted_password`, `salt`, `realname`, `show_email`)
                SELECT `id`, `name`, `created_at`, `updated_at`, `encrypted_password`, `salt`, `realname`, `show_email`
                FROM asi_nairobi.clients WHERE id = var_client_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('clients', var_client_id, var_client_id, NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('clients', 'id', var_client_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- Make sure the imported stuff that linked to Nairobi's coreui links to ASI's coreui
    UPDATE commonservices_production.merge_tracking SET new_id = 'a4m6R6lCWr34TGaaWPfx7J' WHERE new_id = 'ayl7ktFvraGj2Dt149vPB8' AND the_table = 'clients';
    -- And delete Nairobi's coreui
    DELETE FROM commonservices_production.clients WHERE id = 'ayl7ktFvraGj2Dt149vPB8';
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Clients merge completed. No conflicts found";
    ELSE
        SELECT "Clients merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************collections*******************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.collections_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.collections_merge()
BEGIN
    -- Declare variables
    DECLARE var_collection_id VARCHAR(255);
    DECLARE var_owner_id INT;
    DECLARE var_client_id VARCHAR(255);
    DECLARE var_updated_by VARCHAR(255);
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the channels cursor
    DECLARE collections_cursor CURSOR FOR SELECT `id`, `owner_id`, `client_id`, `updated_by` FROM asi_nairobi.collections;
    -- Open collections cursor
    OPEN collections_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE collections_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch collection id
        FETCH collections_cursor INTO var_collection_id, var_owner_id, var_client_id, var_updated_by;
        -- Check for and log any collections conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.collections WHERE id = var_collection_id) THEN
            -- No conflicts, insert collection
            INSERT INTO commonservices_production.collections (`id`, `read_only`, `client_id`, `created_at`, `updated_at`, `owner_id`, `title`, `metadata`, `indestructible`, `tags`, `updated_by`, `priv`)
                SELECT `id`, `read_only`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_client_id AND the_table = 'clients'), `created_at`, `updated_at`,
                (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_owner_id AND the_table = 'people'), `title`, `metadata`, `indestructible`, `tags`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_updated_by AND the_table = 'clients'), `priv`
                FROM asi_nairobi.collections WHERE id = var_collection_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('collections', var_collection_id, var_collection_id, NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('collections', 'id', var_collection_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Collections merge completed. No conflicts found";
    ELSE
        SELECT "Collections merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************roles*************************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.roles_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.roles_merge()
BEGIN
    -- Declare variables
    DECLARE var_role_id INT;
    DECLARE var_person_id INT;
    DECLARE var_client_id VARCHAR(255);
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the roles cursor
    DECLARE roles_cursor CURSOR FOR SELECT id, person_id, client_id FROM asi_nairobi.roles;
    -- Open roles cursor
    OPEN roles_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE roles_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch roles id
        FETCH roles_cursor INTO var_role_id, var_person_id, var_client_id;
        -- Check for and log any roles conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.roles WHERE location_security_token = (SELECT location_security_token FROM asi_nairobi.roles WHERE id = var_role_id)) THEN
            -- No conflicts, insert role
            INSERT INTO commonservices_production.roles (`person_id`, `client_id`, `title`, `created_at`, `updated_at`, `terms_version`, `location_security_token`)
                SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_client_id AND the_table = 'clients'), `title`, `created_at`, `updated_at`, `terms_version`, `location_security_token`
                FROM asi_nairobi.roles WHERE id = var_role_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('roles', var_role_id, LAST_INSERT_ID(), NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('roles', 'location_security_token', var_role_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Roles merge completed. No conflicts found";
    ELSE
        SELECT "Roles merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************user_subscriptions************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.user_subscriptions_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.user_subscriptions_merge()
BEGIN
    -- Declare variables
    DECLARE var_user_subscription_id INT;
    DECLARE var_person_id INT;
    DECLARE var_channel_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the user_subscriptions cursor
    DECLARE user_subscriptions_cursor CURSOR FOR SELECT id, person_id, channel_id FROM asi_nairobi.user_subscriptions;
    -- Open user_subscriptions cursor
    OPEN user_subscriptions_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE user_subscriptions_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch user_subscriptions id
        FETCH user_subscriptions_cursor INTO var_user_subscription_id, var_person_id, var_channel_id;
        -- Insert subscription
        INSERT INTO commonservices_production.user_subscriptions (`person_id`, `channel_id`, `created_at`, `updated_at`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_channel_id AND the_table = 'channels'), `created_at`, `updated_at`
            FROM asi_nairobi.user_subscriptions WHERE id = var_user_subscription_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('user_subscriptions', var_user_subscription_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "User subscriptions merge completed. No conflicts found";
END $$


-- ******************************************************************************
-- ************************************sessions**********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.sessions_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.sessions_merge()
BEGIN
    -- Declare variables
    DECLARE var_session_id INT;
    DECLARE var_person_id INT;
    DECLARE var_client_id VARCHAR(255);
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the sessions cursor
    DECLARE sessions_cursor CURSOR FOR SELECT id, person_id, client_id FROM asi_nairobi.sessions;
    -- Open sessions cursor
    OPEN sessions_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE sessions_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch sessions id
        FETCH sessions_cursor INTO var_session_id, var_person_id, var_client_id;
        -- Insert session
        INSERT INTO commonservices_production.sessions (`person_id`, `ip_address`, `path`, `created_at`, `updated_at`, `client_id`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), `ip_address`, `path`, `created_at`, `updated_at`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_client_id AND the_table = 'clients')
            FROM asi_nairobi.sessions WHERE id = var_session_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('sessions', var_session_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Sessions merge completed";
END $$


-- ******************************************************************************
-- ************************************person_names******************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.person_names_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.person_names_merge()
BEGIN
    -- Declare variables
    DECLARE var_person_names_id INT;
    DECLARE var_person_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the person_names cursor
    DECLARE person_names_cursor CURSOR FOR SELECT id, person_id FROM asi_nairobi.person_names;
    -- Open person_names cursor
    OPEN person_names_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE person_names_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch person_names id
        FETCH person_names_cursor INTO var_person_names_id, var_person_id;
        -- Insert person names
        INSERT INTO commonservices_production.person_names (`given_name`, `family_name`, `created_at`, `updated_at`, `person_id`)
            SELECT `given_name`, `family_name`, `created_at`, `updated_at`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people')
            FROM asi_nairobi.person_names WHERE id = var_person_names_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('person_names', var_person_names_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Person names merge completed";
END $$


-- ******************************************************************************
-- ************************************memberships*******************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.memberships_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.memberships_merge()
BEGIN
    -- Declare variables
    DECLARE var_membership_id INT;
    DECLARE var_person_id INT;
    DECLARE var_group_id VARCHAR(255);
    DECLARE var_inviter_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the memberships cursor
    DECLARE memberships_cursor CURSOR FOR SELECT id, person_id, group_id, inviter_id FROM asi_nairobi.memberships;
    -- Open memberships cursor
    OPEN memberships_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE memberships_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch memberships id
        FETCH memberships_cursor INTO var_membership_id, var_person_id, var_group_id, var_inviter_id;
        -- Insert membership
        INSERT INTO commonservices_production.memberships (`person_id`, `group_id`, `accepted_at`, `admin_role`,`created_at`, `updated_at`, `status`, `inviter_id`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_group_id AND the_table = 'groups'), `accepted_at`, `admin_role`, `created_at`, `updated_at`, `status`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_inviter_id AND the_table = 'people')
            FROM asi_nairobi.memberships WHERE id = var_membership_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('memberships', var_membership_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Memberships merge completed";
END $$


-- ******************************************************************************
-- ************************************groups************************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.groups_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.groups_merge()
BEGIN
    -- Declare variables
    DECLARE var_group_id VARCHAR(255);
    DECLARE var_creator_id INT;
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the groups cursor
    DECLARE groups_cursor CURSOR FOR SELECT id, creator_id FROM asi_nairobi.groups;
    -- Open groups cursor
    OPEN groups_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE groups_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch groups id
        FETCH groups_cursor INTO var_group_id, var_creator_id;
        -- Check for and log any groups conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.groups WHERE id = var_group_id) THEN
            -- Insert group
            INSERT INTO commonservices_production.groups (`id`, `title`, `creator_id`, `group_type`, `created_at`, `updated_at`, `description`)
                SELECT `id`, `title`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_creator_id AND the_table = 'people'), `group_type`, `created_at`, `updated_at`, `description`
                FROM asi_nairobi.groups WHERE id = var_group_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('groups', var_group_id, var_group_id, NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('groups', 'id', var_group_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Groups merge completed. No conflicts found";
    ELSE
        SELECT "Groups merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************channels**********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.channels_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.channels_merge()
BEGIN
    -- Declare variables
    DECLARE var_channel_id INT;
    DECLARE var_person_id INT;
    DECLARE var_app_id VARCHAR(255);
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the channels cursor
    DECLARE channels_cursor CURSOR FOR SELECT id, owner_id, creator_app_id FROM asi_nairobi.channels;
    -- Open channels cursor
    OPEN channels_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE channels_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch channels id
        FETCH channels_cursor INTO var_channel_id, var_person_id, var_app_id;
        -- Check for and log any channels conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.channels WHERE guid = (SELECT guid FROM asi_nairobi.channels WHERE id = var_channel_id)) THEN
            -- No conflicts, insert channel
            INSERT INTO commonservices_production.channels (`name`, `description`, `owner_id`, `channel_type`, `created_at`, `updated_at`, `creator_app_id`, `guid`,
                `delta`, `hidden`)
                SELECT `name`, `description`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), `channel_type`, `created_at`, `updated_at`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_app_id AND the_table = 'clients'), `guid`,
                `delta`, `hidden`
                FROM asi_nairobi.channels WHERE id = var_channel_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('channels', var_channel_id, LAST_INSERT_ID(), NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('channels', 'guid', var_channel_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Channels merge completed. No conflicts found";
    ELSE
        SELECT "Channels merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************messages**********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.messages_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.messages_merge()
BEGIN
    -- Declare variables
    DECLARE var_message_id INT;
    DECLARE var_channel_id INT;
    DECLARE var_person_id INT;
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the messages cursor
    DECLARE messages_cursor CURSOR FOR SELECT id, channel_id, poster_id FROM asi_nairobi.messages;
    -- And the cursor for messages that have a reference_to
    DECLARE references_cursor CURSOR FOR SELECT id, reference_to FROM asi_nairobi.messages WHERE reference_to != 'NULL';
    -- Open messages cursor
    OPEN messages_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE messages_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch messages id
        FETCH messages_cursor INTO var_message_id, var_channel_id, var_person_id;
        -- Check for and log any messages conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.messages WHERE guid = (SELECT guid FROM asi_nairobi.messages WHERE id = var_message_id)) THEN
            -- No conflicts, insert message
            INSERT INTO commonservices_production.messages (`title`, `content_type`, `body`, `poster_id`, `channel_id`, `created_at`, `updated_at`, `reference_to`, `attachment`, `guid`, `delta`)
                SELECT `title`, `content_type`, `body`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_channel_id AND the_table = 'channels'), `created_at`, `updated_at`, `reference_to`, `attachment`, `guid`, `delta`
                FROM asi_nairobi.messages WHERE id = var_message_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('messages', var_message_id, LAST_INSERT_ID(), NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('messages', 'guid', var_message_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- Update links for reference_to field
    -- Reset counter
    SET current_row = 0;
    -- Open references cursor
    OPEN references_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    references_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE references_cursor;
            LEAVE references_loop;
        END IF;
        -- Fetch messages id, reuse channel_id variable to store reference_to
        FETCH references_cursor INTO var_message_id, var_channel_id;
        -- Update reference_to
        UPDATE commonservices_production.messages SET `reference_to` = (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_channel_id AND the_table = 'messages') WHERE id = (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_message_id AND the_table = 'messages');
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP references_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Messages merge completed. No conflicts found";
    ELSE
        SELECT "Messages merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************addresses*********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.addresses_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.addresses_merge()
BEGIN
    -- Declare variables
    DECLARE var_address_id INT;
    DECLARE var_owner_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the addresses cursor
    DECLARE addresses_cursor CURSOR FOR SELECT id, owner_id FROM asi_nairobi.addresses;
    -- Open addresses cursor
    OPEN addresses_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE addresses_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch addresses id
        FETCH addresses_cursor INTO var_address_id, var_owner_id;
        -- Insert address
        INSERT INTO commonservices_production.addresses (`street_address`, `postal_code`, `locality`, `owner_id`, `owner_type`, `created_at`, `updated_at`)
            SELECT `street_address`, `postal_code`, `locality`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_owner_id AND the_table = 'people'), `owner_type`, `created_at`, `updated_at`
            FROM asi_nairobi.addresses WHERE id = var_address_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('addresses', var_address_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Addresses merge completed";
END $$


-- ******************************************************************************
-- ************************************conditions********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.conditions_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.conditions_merge()
BEGIN
    -- Declare variables
    DECLARE var_condition_id VARCHAR(255);
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the conditions cursor
    DECLARE conditions_cursor CURSOR FOR SELECT id FROM asi_nairobi.conditions;
    -- Open conditions cursor
    OPEN conditions_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE conditions_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch condition id
        FETCH conditions_cursor INTO var_condition_id;
        -- Check for and log any channels conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.conditions WHERE id = var_condition_id) THEN
            -- No conflicts, insert condition
            INSERT INTO commonservices_production.conditions (`id`, `condition_type`, `condition_value`, `created_at`, `updated_at`)
                SELECT `id`, `condition_type`, `condition_value`, `created_at`, `updated_at`
                FROM asi_nairobi.conditions WHERE id = var_condition_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('conditions', var_condition_id, var_condition_id, NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('conditions', 'id', var_condition_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Conditions merge completed. No conflicts found";
    ELSE
        SELECT "Conditions merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$


-- ******************************************************************************
-- ************************************connections*******************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.connections_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.connections_merge()
BEGIN
    -- Declare variables
    DECLARE var_connection_id INT;
    DECLARE var_person_id INT;
    DECLARE var_contact_id VARCHAR(255);
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the connections cursor
    DECLARE connections_cursor CURSOR FOR SELECT id, person_id, contact_id FROM asi_nairobi.connections;
    -- Open connections cursor
    OPEN connections_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE connections_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch connections id
        FETCH connections_cursor INTO var_connection_id, var_person_id, var_contact_id;
        -- Insert connection
        INSERT INTO commonservices_production.connections (`person_id`, `contact_id`, `status`, `created_at`, `updated_at`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_contact_id AND the_table = 'people'), `status`, `created_at`, `updated_at`
            FROM asi_nairobi.connections WHERE id = var_connection_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('connections', var_connection_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Connections merge completed";
END $$


-- ******************************************************************************
-- ************************************feedbacks********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.feedbacks_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.feedbacks_merge()
BEGIN
    -- Declare variables
    DECLARE var_feedback_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the feedbacks cursor
    DECLARE feedbacks_cursor CURSOR FOR SELECT id FROM asi_nairobi.feedbacks;
    -- Open feedbacks cursor
    OPEN feedbacks_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE feedbacks_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch feedback id
        FETCH feedbacks_cursor INTO var_feedback_id;
        -- Insert feedback
        INSERT INTO commonservices_production.feedbacks (`content`, `author_id`, `url`, `is_handled`, `created_at`, `updated_at`)
            SELECT `content`, `author_id`, `url`, `is_handled`, `created_at`, `updated_at`
            FROM asi_nairobi.feedbacks WHERE id = var_feedback_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('feedbacks', var_feedback_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Feedbacks merge completed";
END $$


-- ******************************************************************************
-- ************************************group_search_handles********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.group_search_handles_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.group_search_handles_merge()
BEGIN
    -- Declare variables
    DECLARE var_group_search_handle_id INT;
    DECLARE var_group_id VARCHAR(255);
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the group_search_handles cursor
    DECLARE group_search_handles_cursor CURSOR FOR SELECT id, group_id FROM asi_nairobi.group_search_handles;
    -- Open group_search_handles cursor
    OPEN group_search_handles_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE group_search_handles_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch group_search_handle id
        FETCH group_search_handles_cursor INTO var_group_search_handle_id, var_group_id;
        -- Insert group_search_handle
        INSERT INTO commonservices_production.group_search_handles (`group_id`, `delta`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_group_id AND the_table = 'groups'), `delta`
            FROM asi_nairobi.group_search_handles WHERE id = var_group_search_handle_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('group_search_handles', var_group_search_handle_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Group search handles merge completed";
END $$


-- ******************************************************************************
-- ************************************group_subscriptions********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.group_subscriptions_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.group_subscriptions_merge()
BEGIN
    -- Declare variables
    DECLARE var_group_subscription_id INT;
    DECLARE var_group_id VARCHAR(255);
    DECLARE var_channel_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the group_subscriptions cursor
    DECLARE group_subscriptions_cursor CURSOR FOR SELECT id, group_id, channel_id FROM asi_nairobi.group_subscriptions;
    -- Open group_subscriptions cursor
    OPEN group_subscriptions_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE group_subscriptions_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch group_subscription id
        FETCH group_subscriptions_cursor INTO var_group_subscription_id, var_group_id, var_channel_id;
        -- Insert group_subscriptions
        INSERT INTO commonservices_production.group_subscriptions (`group_id`, `channel_id`, `created_at`, `updated_at`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_group_id AND the_table = 'groups'), (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_channel_id AND the_table = 'channels'), `created_at`, `updated_at`
            FROM asi_nairobi.group_subscriptions WHERE id = var_group_subscription_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('group_subscriptions', var_group_subscription_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Group subscriptions merge completed";
END $$


-- ******************************************************************************
-- ************************************locations**********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.locations_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.locations_merge()
BEGIN
    -- Declare variables
    DECLARE var_location_id INT;
    DECLARE var_person_id INT;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the locations cursor
    DECLARE locations_cursor CURSOR FOR SELECT id, person_id FROM asi_nairobi.locations;
    -- Open locations cursor
    OPEN locations_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE locations_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch location id
        FETCH locations_cursor INTO var_location_id, var_person_id;
        -- Insert location
        INSERT INTO commonservices_production.locations (`person_id`, `latitude`, `longitude`, `label`, `created_at`, `updated_at`, `accuracy`)
            SELECT (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), `latitude`, `longitude`, `label`, `created_at`, `updated_at`, `accuracy`
            FROM asi_nairobi.locations WHERE id = var_location_id;
        -- Tracking
        INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('locations', var_location_id, LAST_INSERT_ID(), NOW());
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    SELECT "Locations merge completed";
END $$


-- ******************************************************************************
-- ************************************images**********************************
-- ******************************************************************************
DROP PROCEDURE IF EXISTS commonservices_production.images_merge $$
CREATE DEFINER=root@localhost PROCEDURE commonservices_production.images_merge()
BEGIN
    -- Declare variables
    DECLARE var_image_id VARCHAR(255);
    DECLARE var_person_id INT;
    DECLARE conflict_counter INT DEFAULT 0;
    -- Declare variables used just for cursor and loop control
    DECLARE current_row INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    -- Declare the images cursor
    DECLARE images_cursor CURSOR FOR SELECT id, person_id FROM asi_nairobi.images;
    -- Open images cursor
    OPEN images_cursor;
    SELECT FOUND_ROWS() INTO num_rows;
    -- Start loop
    the_loop: LOOP
        -- If no more rows, exit loop
        IF current_row >= num_rows THEN
            CLOSE images_cursor;
            LEAVE the_loop;
        END IF;
        -- Fetch image id
        FETCH images_cursor INTO var_image_id, var_person_id;
        -- Check for and log any images conflicts
        IF NOT EXISTS (SELECT 1 FROM commonservices_production.images WHERE id = var_image_id) THEN
            -- No conflicts, insert image
            INSERT INTO commonservices_production.images (`id`, `filename`, `data`, `created_at`, `person_id`, `small_thumb`, `large_thumb`)
                SELECT `id`, `filename`, `data`, `created_at`, (SELECT new_id FROM commonservices_production.merge_tracking WHERE old_id = var_person_id AND the_table = 'people'), `small_thumb`, `large_thumb`
                FROM asi_nairobi.images WHERE id = var_image_id;
            -- Tracking
            INSERT INTO commonservices_production.merge_tracking (the_table, old_id, new_id, logged_at) VALUES ('images', var_image_id, var_image_id, NOW());
        ELSE INSERT INTO commonservices_production.merge_conflicts (the_table, the_field, the_id, logged_at) VALUES ('images', 'id', var_image_id, NOW());
        SET conflict_counter = conflict_counter + 1;
        END IF;
        -- Increment loop counter
        SET current_row = current_row + 1;
    -- End loop
    END LOOP the_loop;
    -- End
    IF conflict_counter = 0 THEN
        SELECT "Images merge completed. No conflicts found";
    ELSE
        SELECT "Images merge completed.", conflict_counter, "conflicts found";
    END IF;
END $$

DELIMITER ;

-- Go!
CALL commonservices_production.merge_prep();
CALL commonservices_production.clients_merge();
CALL commonservices_production.people_merge();
CALL commonservices_production.groups_merge();
CALL commonservices_production.channels_merge();
CALL commonservices_production.collections_merge();
CALL commonservices_production.messages_merge();
CALL commonservices_production.addresses_merge();
CALL commonservices_production.images_merge();
CALL commonservices_production.locations_merge();
CALL commonservices_production.group_subscriptions_merge();
CALL commonservices_production.group_search_handles_merge();
CALL commonservices_production.feedbacks_merge();
CALL commonservices_production.connections_merge();
CALL commonservices_production.conditions_merge();
CALL commonservices_production.roles_merge();
CALL commonservices_production.user_subscriptions_merge();
CALL commonservices_production.sessions_merge();
CALL commonservices_production.person_names_merge();
CALL commonservices_production.memberships_merge();

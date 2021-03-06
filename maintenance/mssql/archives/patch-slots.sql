--
-- Slots represent an n:m relation between revisions and content objects.
-- A content object can have a specific "role" in one or more revisions.
-- Each revision can have multiple content objects, each having a different role.
--
CREATE TABLE /*_*/slots (

  -- reference to rev_id
  slot_revision_id bigint NOT NULL,

  -- reference to role_id
  slot_role_id smallint NOT NULL CONSTRAINT FK_slots_slot_role FOREIGN KEY REFERENCES slot_roles(role_id),

  -- reference to content_id
  slot_content_id bigint NOT NULL CONSTRAINT FK_slots_content_id FOREIGN KEY REFERENCES content(content_id),

  -- The revision ID of the revision that originated the slot's content.
  -- To find revisions that changed slots, look for slot_origin = slot_revision_id.
  slot_origin bigint NOT NULL,

  CONSTRAINT PK_slots PRIMARY KEY (slot_revision_id, slot_role_id)
);

-- Index for finding revisions that modified a specific slot
CREATE INDEX /*i*/slot_revision_origin_role ON /*_*/slots (slot_revision_id, slot_origin, slot_role_id);

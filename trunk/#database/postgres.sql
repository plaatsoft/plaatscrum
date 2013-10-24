CREATE ROLE plaatscrum LOGIN
  ENCRYPTED PASSWORD 'md52b67f953b52af9813e79236f9cb15e31'
  SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;

CREATE DATABASE plaatscrum
  WITH OWNER = plaatscrum
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'Dutch_Netherlands.1252'
       LC_CTYPE = 'Dutch_Netherlands.1252'
       CONNECTION LIMIT = -1;
  

-- Table: history

CREATE TABLE history
(
  history_id serial NOT NULL,
  user_id integer,
  story_id integer,
  status_old integer,
  status_new integer,
  date timestamp without time zone,
  CONSTRAINT history_pkey PRIMARY KEY (history_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE history
  OWNER TO plaatscrum;
GRANT ALL ON TABLE history TO plaatscrum;

CREATE INDEX history_idx
  ON history
  USING btree
  (history_id);
  
  
-- Table: member

CREATE TABLE member
(
  member_id serial NOT NULL,
  user_id integer,
  username text,
  password text,
  last_login timestamp without time zone,
  last_activity timestamp without time zone,
  requests integer,
  deleted integer,
  CONSTRAINT member_pkey PRIMARY KEY (member_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE member
  OWNER TO plaatscrum;
GRANT ALL ON TABLE member TO plaatscrum;

CREATE INDEX member_idx
  ON member
  USING btree
  (member_id );
  
    
-- Table: project

CREATE TABLE project
(
  project_id serial NOT NULL,
  name text,
  public integer,
  deleted integer,
  CONSTRAINT project_pkey PRIMARY KEY (project_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE project
  OWNER TO plaatscrum;
GRANT ALL ON TABLE project TO plaatscrum;

CREATE INDEX project_idx
  ON project
  USING btree
  (project_id);

-- Table: project_user

CREATE TABLE project_user
(
  user_id integer NOT NULL,
  project_id integer NOT NULL,
  role_id integer,
  CONSTRAINT project_user_pkey PRIMARY KEY (user_id , project_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE project_user
  OWNER TO plaatscrum;
GRANT ALL ON TABLE project_user TO plaatscrum;


-- Table: released

CREATE TABLE released
(
  release_id serial NOT NULL,
  project_id integer,
  name text,
  note text,
  deleted integer,
  CONSTRAINT released_pkey PRIMARY KEY (release_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE released
  OWNER TO plaatscrum;
GRANT ALL ON TABLE released TO plaatscrum;

CREATE INDEX release_idx
  ON released
  USING btree
  (release_id);
  
  
-- Table: role

CREATE TABLE role
(
  role_id integer NOT NULL,
  project_edit integer,
  story_add integer,
  story_edit integer,
  story_delete integer,
  story_export integer,
  story_import integer,
  CONSTRAINT role_pkey PRIMARY KEY (role_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE role
  OWNER TO plaatscrum;
GRANT ALL ON TABLE role TO plaatscrum;

CREATE INDEX role_role_id_idx
  ON role
  USING btree
  (role_id );


-- Table: session

CREATE TABLE session
(
  session_id serial NOT NULL,
  member_id integer,
  session text,
  date timestamp without time zone,
  CONSTRAINT session_pkey PRIMARY KEY (session_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE session
  OWNER TO plaatscrum;
GRANT ALL ON TABLE session TO plaatscrum;

CREATE INDEX session_idx
  ON session
  USING btree
  (session_id);


-- Table: sprint

CREATE TABLE sprint
(
  sprint_id serial NOT NULL,
  project_id integer,
  release_id integer,
  "number" integer,
  description text,
  start_date date,
  end_date date,
  deleted integer,
  CONSTRAINT sprint_pkey PRIMARY KEY (sprint_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE sprint
  OWNER TO plaatscrum;
GRANT ALL ON TABLE sprint TO plaatscrum;

CREATE INDEX sprint_idx
  ON sprint
  USING btree
  (sprint_id);


-- Table: story

CREATE TABLE story
(
  story_id integer NOT NULL,
  user_id integer,
  "number" integer,
  summary text,
  description text,
  status integer,
  points integer,
  sprint_id integer,
  project_id integer,
  reference text,
  date date,
  deleted integer,
  prio integer,
  type integer,
  story_story_id integer,
  CONSTRAINT story_pkey PRIMARY KEY (story_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE story
  OWNER TO plaatscrum;
GRANT ALL ON TABLE story TO plaatscrum;

CREATE INDEX story_idx
  ON story
  USING btree
  (story_id);
  

-- Table: tuser

CREATE TABLE tuser
(
  user_id integer NOT NULL,
  name text,
  email text,
  role_id integer,
  project_id integer,
  sprint_id integer,
  menu_id integer,
  page_id integer,
  owner integer,
  prio integer,
  language integer,
  type text,
  status text,
  CONSTRAINT user_pkey PRIMARY KEY (user_id )
)
WITH (
  OIDS=FALSE
);

ALTER TABLE tuser
  OWNER TO plaatscrum;
GRANT ALL ON TABLE tuser TO plaatscrum;

CREATE INDEX tuser_idx
  ON tuser
  USING btree
  (user_id);
  
  
#tabel musisi
create database project;

create table users(
	user_id serial,
	username varchar(50) not null unique,
	password varchar(255) not null,
	email varchar(255) not null,
    reg_date timestamp DEFAULT CURRENT_TIMESTAMP,
	constraint user_userid_pk primary key (user_id)
);

create table facilitator(
	facil_id int not null,
	constraint facil_facilid_pk primary key (facil_id),
	constraint facil_facilid_fk foreign key (facil_id) references users(user_id)
);

create table govtagency(
	govtagency_id  serial not null unique,
	govtagency_name varchar(255) not null,
	govtagency_address text not null,
	govtagency_desc text not null
);
INSERT INTO govtagency(govtagency_name,govtagency_address,govtagency_desc) values ('Kementrian C', 'Jalan Jalan No. 3', 'Mengurus kesehatan');
INSERT INTO govtagency(govtagency_name,govtagency_address,govtagency_desc) values ('Kementrian A', 'Jalan Jalan No. 1', 'Mengurus keuangan');
INSERT INTO govtagency(govtagency_name,govtagency_address,govtagency_desc) values ('Kementrian B', 'Jalan Jalan No. 2', 'Mengurus keamanan');
INSERT INTO govtagency(govtagency_name,govtagency_address,govtagency_desc) values ('Kementrian Latihan', 'Jalan Jalan No. 4', 'ASN baru masuk di sini');

create table asn(
	asn_id int not null,
	workplace_id int not null default 4,
	constraint asn_asnid_pk primary key (asn_id),
	constraint asn_asnid_fk foreign key (asn_id) references users(user_id),
	constraint asn_govtid_fk foreign key (workplace_id) references govtagency(govtagency_id)
);
INSERT INTO facilitator(facil_id) values(1);
INSERT INTO facilitator(facil_id) values(2);
select users.user_id, users.username from users join facilitator on users.user_id=facilitator.facil_id;
INSERT INTO asn(asn_id,workplace_id) values(1,3);
select users.user_id, users.username from users join asn on users.user_id=asn.asn_id;
ins
INSERT INTO asn(asn_id,workplace_id)values(3,2);
 
create table issues(
	issue_id serial,
	issue_name varchar(255) not null,
	issue_desc text not null,
	proposer_id int not null,
	respagency_id int not null,
	constraint issue_issueid_pk primary key (issue_id),
	constraint issue_agencyid_fk foreign key (respagency_id) references govtagency(govtagency_id),
	constraint issue_proposerid_fk foreign key (proposer_id) references users(user_id)
);

create table alokasi_fasilitator(
	allocissue_id int not null,
	facilitator_id int not null,
	constraint alloc_issueid_pk primary key (allocissue_id),
	constraint alloc_issueid_fk foreign key (allocissue_id) references issues(issue_id),
	constraint alloc_facilid_fk foreign key (facilitator_id) references facilitator(facil_id)
);

create table approval_asn(
	approvedissue_id int not null,
	asnapprover_id int not null,
	constraint approval_issueid_pk primary key (approvedissue_id),
	constraint approval_issueid_fk foreign key (approvedissue_id) references issues(issue_id),
	constraint approval_asnid_fk foreign key (asnapprover_id) references asn(asn_id)
);
INSERT into alokasi_fasilitator(allocissue_id,facilitator_id) VALUES(1,1);
INSERT INTO approval_asn(approvedissue_id,asnapprover_id) VALUES(1,1);
INSERT into alokasi_fasilitator(allocissue_id,facilitator_id) VALUES(2,2);
INSERT INTO approval_asn(approvedissue_id,asnapprover_id) VALUES(3,3);

CREATE TABLE survey(
	survey_id serial,
	sur_issue_id int NOT NULL,
	survey_title varchar(255) NOT NULL unique,
	survey_desc text NOT NULL,
	sur_creator_id int NOT NULL,
	constraint survey_surveyid_pk primary key (survey_id),
	constraint survey_surissueid_fk foreign key (sur_issue_id) references issues(issue_id),
	constraint survey_surcreatorid_fk foreign key (sur_creator_id) references users(user_id)
);

CREATE TABLE jawaban(
	jawaban_id serial,
	survey_id int NOT NULL,
	jawaban text not null,
	constraint jawaban_jawabanid_pk primary key (jawaban_id),
	constraint jawaban_surveyid_fk foreign key (survey_id) references survey(survey_id) ON DELETE CASCADE
);

CREATE TABLE rekapitulasi_jawaban(
	user_id int NOT NULL,
	jawaban_id int NOT NULL,
	constraint rekap_jawabanuserid_pk primary key (jawaban_id,user_id),
	constraint rekap_jawabanid_fk foreign key (jawaban_id) references jawaban(jawaban_id) ON DELETE CASCADE,
	constraint rekap_userid_fk foreign key (user_id) references users(user_id)
);

CREATE TABLE topik(
	topic_id serial,
	issue_id int NOT NULL,
	topic_name varchar(255) NOT NULL,
	topic_date timestamp default CURRENT_TIMESTAMP NOT NULL,
	topic_creator_id int NOT NULL,
	constraint topic_topicid_pk primary key (topic_id),
	constraint topic_issueid_fk foreign key (issue_id) references issues(issue_id),
	constraint topic_creatorid_fk foreign key (topic_creator_id) references users(user_id)
);

CREATE TABLE post(
	post_id serial,
	topic_id int NOT NULL,
	post text not null,
	post_date timestamp default CURRENT_TIMESTAMP NOT NULL,
	post_creator_id int NOT NULL,
	constraint post_postid_pk primary key (post_id),
	constraint post_topicid_fk foreign key (topic_id) references topik(topic_id),
	constraint post_creatorid_fk foreign key (post_creator_id) references users(user_id)
);

CREATE TABLE konsultasi(
	konsultasi_id serial,
	issue_id int not null,
	waktu_konsultasi timestamp not null,
	platform_konsultasi varchar(255) not null,
	alamat_tautan_konsultasi varchar(255) not null,
	facil_id int not null,
	constraint cons_consulid_pk primary key (konsultasi_id),
	constraint cons_facilid_fk foreign key (facil_id) references facilitator(facil_id),
	constraint cons_issueid_fk foreign key (issue_id) references issues(issue_id)
);
<?php
/**
 * Microsoft SQL Server-specific installer.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Deployment
 */

use Wikimedia\Rdbms\DatabaseMssql;

/**
 * Class for setting up the MediaWiki database using Microsoft SQL Server.
 *
 * @ingroup Deployment
 * @since 1.23
 */

class MssqlUpdater extends DatabaseUpdater {

	/**
	 * @var DatabaseMssql
	 */
	protected $db;

	protected function getCoreUpdateList() {
		return [
			// 1.23
			[ 'addField', 'mwuser', 'user_password_expires', 'patch-user_password_expires.sql' ],

			// 1.24
			[ 'addField', 'page', 'page_lang', 'patch-page_page_lang.sql' ],

			// 1.25
			[ 'dropTable', 'hitcounter' ],
			[ 'dropField', 'site_stats', 'ss_total_views', 'patch-drop-ss_total_views.sql' ],
			[ 'dropField', 'page', 'page_counter', 'patch-drop-page_counter.sql' ],
			// scripts were updated in 1.27 due to SQL errors; retaining old updatekeys so that people
			// updating from 1.23->1.25->1.27 do not execute these scripts twice even though the
			// updatekeys no longer make sense as they are.
			[ 'updateSchema', 'categorylinks', 'cl_type-category_types-ck',
				'patch-categorylinks-constraints.sql' ],
			[ 'updateSchema', 'filearchive', 'fa_major_mime-major_mime-ck',
				'patch-filearchive-constraints.sql' ],
			[ 'updateSchema', 'oldimage', 'oi_major_mime-major_mime-ck',
				'patch-oldimage-constraints.sql' ],
			[ 'updateSchema', 'image', 'img_major_mime-major_mime-ck', 'patch-image-constraints.sql' ],
			[ 'updateSchema', 'uploadstash', 'us_media_type-media_type-ck',
				'patch-uploadstash-constraints.sql' ],

			[ 'modifyField', 'image', 'img_major_mime',
				'patch-img_major_mime-chemical.sql' ],
			[ 'modifyField', 'oldimage', 'oi_major_mime',
				'patch-oi_major_mime-chemical.sql' ],
			[ 'modifyField', 'filearchive', 'fa_major_mime',
				'patch-fa_major_mime-chemical.sql' ],

			// 1.27
			[ 'dropTable', 'msg_resource_links' ],
			[ 'dropTable', 'msg_resource' ],
			[ 'addField', 'watchlist', 'wl_id', 'patch-watchlist-wl_id.sql' ],
			[ 'dropField', 'mwuser', 'user_options', 'patch-drop-user_options.sql' ],
			[ 'addTable', 'bot_passwords', 'patch-bot_passwords.sql' ],
			[ 'addField', 'pagelinks', 'pl_from_namespace', 'patch-pl_from_namespace.sql' ],
			[ 'addField', 'templatelinks', 'tl_from_namespace', 'patch-tl_from_namespace.sql' ],
			[ 'addField', 'imagelinks', 'il_from_namespace', 'patch-il_from_namespace.sql' ],
			[ 'dropIndex', 'categorylinks', 'cl_collation', 'patch-kill-cl_collation_index.sql' ],
			[ 'addIndex', 'categorylinks', 'cl_collation_ext',
				'patch-add-cl_collation_ext_index.sql' ],
			[ 'dropField', 'recentchanges', 'rc_cur_time', 'patch-drop-rc_cur_time.sql' ],
			[ 'addField', 'page_props', 'pp_sortkey', 'patch-pp_sortkey.sql' ],
			[ 'updateSchema', 'oldimage', 'oldimage varchar', 'patch-oldimage-schema.sql' ],
			[ 'updateSchema', 'filearchive', 'filearchive varchar', 'patch-filearchive-schema.sql' ],
			[ 'updateSchema', 'image', 'image varchar', 'patch-image-schema.sql' ],
			[ 'updateSchema', 'recentchanges', 'recentchanges-drop-fks',
				'patch-recentchanges-drop-fks.sql' ],
			[ 'updateSchema', 'logging', 'logging-drop-fks', 'patch-logging-drop-fks.sql' ],
			[ 'updateSchema', 'archive', 'archive-drop-fks', 'patch-archive-drop-fks.sql' ],

			// 1.28
			[ 'addIndex', 'recentchanges', 'rc_name_type_patrolled_timestamp',
				'patch-add-rc_name_type_patrolled_timestamp_index.sql' ],
			[ 'addField', 'change_tag', 'ct_id', 'patch-change_tag-ct_id.sql' ],

			// 1.29
			[ 'addField', 'externallinks', 'el_index_60', 'patch-externallinks-el_index_60.sql' ],
			[ 'dropIndex', 'oldimage', 'oi_name_archive_name',
				'patch-alter-table-oldimage.sql' ],

			// 1.30
			[ 'modifyField', 'image', 'img_media_type', 'patch-add-3d.sql' ],
			[ 'addIndex', 'site_stats', 'PRIMARY', 'patch-site_stats-pk.sql' ],

			// Should have been in 1.30
			[ 'addTable', 'comment', 'patch-comment-table.sql' ],
			// This field was added in 1.31, but is put here so it can be used by 'migrateComments'
			[ 'addField', 'image', 'img_description_id', 'patch-image-img_description_id.sql' ],
			// Should have been in 1.30
			[ 'migrateComments' ],

			// 1.31
			[ 'addTable', 'slot_roles', 'patch-slot_roles.sql' ],
			[ 'addTable', 'content_models', 'patch-content_models.sql' ],
			[ 'addTable', 'content', 'patch-content.sql' ],
			[ 'addTable', 'slots', 'patch-slots.sql' ],
			[ 'addField', 'slots', 'slot_origin', 'patch-slot-origin.sql' ],
			[ 'migrateArchiveText' ],
			[ 'addTable', 'actor', 'patch-actor-table.sql' ],
			[ 'migrateActors' ],
			[ 'modifyField', 'revision', 'rev_text_id', 'patch-rev_text_id-default.sql' ],
			[ 'modifyTable', 'site_stats', 'patch-site_stats-modify.sql' ],
			[ 'populateArchiveRevId' ],
			[ 'modifyField', 'recentchanges', 'rc_patrolled', 'patch-rc_patrolled_type.sql' ],
			[ 'addIndex', 'recentchanges', 'rc_namespace_title_timestamp',
				'patch-recentchanges-nttindex.sql' ],

			// 1.32
			[ 'addTable', 'change_tag_def', 'patch-change_tag_def.sql' ],
			[ 'populateExternallinksIndex60' ],
			[ 'modifyfield', 'externallinks', 'el_index_60',
				'patch-externallinks-el_index_60-drop-default.sql' ],
			[ 'runMaintenance', DeduplicateArchiveRevId::class, 'maintenance/deduplicateArchiveRevId.php' ],
			[ 'addField', 'change_tag', 'ct_tag_id', 'patch-change_tag-tag_id.sql' ],
			[ 'addIndex', 'archive', 'ar_revid_uniq', 'patch-archive-ar_rev_id-unique.sql' ],
			[ 'populateContentTables' ],
			[ 'addIndex', 'logging', 'log_type_action', 'patch-logging-log-type-action-index.sql' ],
			[ 'dropIndex', 'logging', 'type_action', 'patch-logging-drop-type-action-index.sql' ],
			[ 'addIndex', 'interwiki', 'PRIMARY', 'patch-interwiki-pk.sql' ],
			[ 'addIndex', 'protected_titles', 'PRIMARY', 'patch-protected_titles-pk.sql' ],
			[ 'addIndex', 'page_props', 'PRIMARY', 'patch-page_props-pk.sql' ],
			[ 'addIndex', 'site_identifiers', 'PRIMARY', 'patch-site_identifiers-pk.sql' ],
			[ 'addIndex', 'recentchanges', 'rc_this_oldid', 'patch-recentchanges-rc_this_oldid-index.sql' ],
			[ 'dropTable', 'transcache' ],
			[ 'runMaintenance', PopulateChangeTagDef::class, 'maintenance/populateChangeTagDef.php' ],
			[ 'addIndex', 'change_tag', 'change_tag_rc_tag_id',
				'patch-change_tag-change_tag_rc_tag_id.sql' ],
			[ 'addField', 'ipblocks', 'ipb_sitewide', 'patch-ipb_sitewide.sql' ],
			[ 'addTable', 'ipblocks_restrictions', 'patch-ipblocks_restrictions-table.sql' ],
			[ 'migrateImageCommentTemp' ],

			// 1.33
			[ 'dropField', 'change_tag', 'ct_tag', 'patch-drop-ct_tag.sql' ],
			[ 'dropTable', 'valid_tag' ],
			[ 'dropTable', 'tag_summary' ],
			[ 'dropField', 'protected_titles', 'pt_reason', 'patch-drop-comment-fields.sql' ],
		];
	}

	protected function applyPatch( $path, $isFullPath = false, $msg = null ) {
		$prevScroll = $this->db->scrollableCursor( false );
		$prevPrep = $this->db->prepareStatements( false );
		parent::applyPatch( $path, $isFullPath, $msg );
		$this->db->scrollableCursor( $prevScroll );
		$this->db->prepareStatements( $prevPrep );
	}

	/**
	 * General schema update for a table that touches more than one field or requires
	 * destructive actions (such as dropping and recreating the table). NOTE: Usage of
	 * this function is highly discouraged, use it's successor DatabaseUpdater::modifyTable
	 * instead.
	 *
	 * @param string $table
	 * @param string $updatekey
	 * @param string $patch
	 * @param bool $fullpath
	 * @return bool
	 */
	protected function updateSchema( $table, $updatekey, $patch, $fullpath = false ) {
		if ( !$this->db->tableExists( $table, __METHOD__ ) ) {
			$this->output( "...$table table does not exist, skipping schema update patch.\n" );
		} elseif ( $this->updateRowExists( $updatekey ) ) {
			$this->output( "...$table already had schema updated by $patch.\n" );
		} else {
			$apply = $this->applyPatch( $patch, $fullpath, "Updating schema of table $table" );
			if ( $apply ) {
				$this->insertUpdateRow( $updatekey );
			}
			return $apply;
		}

		return true;
	}
}

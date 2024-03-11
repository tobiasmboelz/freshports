<?php
	#
	# $Id: watch_list_element.php,v 1.3 2013-01-29 16:02:57 dan Exp $
	#
	# Copyright (c) 1998-2005 DVL Software Limited
	#

// base class for a single item on a watch list
class WatchListElement {

	var $dbh;

	var $watch_list_id;
	var $element_id;

	var $watch_list_count;
	var $user_id;

	var $LocalResult;
	
	var $Debug;


	function __construct($dbh) {
		$this->dbh   = $dbh;
		$this->Debug = 0;
	}
	
	function Delete($UserID, $WatchListID, $ElementID) {
		#
		# Delete an item from a watch list
		#

		#
		# The "subselect" ensures the user can only delete things from their
		# own watch list
		#
		$sql = 'DELETE FROM watch_list_element
                 USING watch_list
		         WHERE watch_list_element.element_id    = $1
		           AND watch_list.id                    = $2
		           AND watch_list.user_id               = $3
		           AND watch_list_element.watch_list_id = watch_list.id';
		if ($this->Debug) echo "<pre>$sql</pre>";
		$result = pg_query_params($this->dbh, $sql, array($ElementID, $WatchListID, $UserID));

		# that worked and we updated exactly one row
		if ($result) {
			$return = pg_affected_rows($result);
		} else {
			$return = -1;
		}

		return $return;
	}


	function DeleteElementFromWatchLists($UserID, $WatchListID, $ElementID) {
		#
		# Delete this element from all watch lists
		#

		#
		# The "subselect" ensures the user can only delete things from their
		# own watch list
		#

		$sql = 'DELETE FROM watch_list_element
                 USING watch_list
		         WHERE watch_list.user_id               = $1
		           AND watch_list.id                    = $2
		           AND watch_list_element.element_id    = $3
		           AND watch_list_element.watch_list_id = watch_list.id';
		if ($this->Debug) echo "<pre>$sql</pre>";
		$result = pg_query_params($this->dbh, $sql, array($UserID, $WatchListID, $ElementID));

		# that worked and we updated exactly one row
		if ($result) {
			$return = pg_affected_rows($result);
		} else {
			$return = -1;
		}

		return $return;
	}

	function DeleteFromDefault($UserID, $ElementID) {
		#
		# Delete an item from all default watch lists
		#

		#
		# The "subselect" ensures the user can only delete things from their
		# own watch list
		#
		$sql = 'DELETE FROM watch_list_element
                 USING watch_list
		         WHERE watch_list_element.element_id    = $1
		           AND watch_list.in_service            = TRUE
		           AND watch_list.user_id               = $2
		           AND watch_list_element.watch_list_id = watch_list.id';

		if ($this->Debug) echo "<pre>$sql</pre>";
		$result = pg_query_params($this->dbh, $sql, array($ElementID, $UserID));

		# that worked and we updated exactly one row
		if ($result) {
			$return = pg_affected_rows($result);
		} else {
			$return = -1;
		}

		return $return;
	}

	function Add($UserID, $WatchListID, $ElementID) {
		#
		# Add an item to a watch list
		#

		#
		# make sure we don't report the duplicate entry error when adding...
		#
		$PreviousReportingLevel = error_reporting(E_ALL ^ E_WARNING);

		#
		# The subselect ensures the user can only add things to their
		# own watch list
		#
		$sql = '
INSERT INTO watch_list_element 
select $1, $2
  from watch_list 
 where user_id = $3
   and id      = $1
   and not exists (
    SELECT watch_list_element.watch_list_id, watch_list_element.element_id
      FROM watch_list_element
     WHERE watch_list_element.watch_list_id = $1
       AND watch_list_element.element_id    = $2)';
		if ($this->Debug) echo "<pre>$sql</pre>";
		$result = pg_query_params($this->dbh, $sql, array($WatchListID, $ElementID, $UserID));
		if ($result) {
			$return = 1;
		} else {
			# If this isn't a duplicate key error, then break
			if (stristr(pg_last_error($this->dbh), "Cannot insert a duplicate key") == '') {
				$return = 1;
			} else {
				$return = 1;
			}
		}

		# I found this sometimes has no value
		if (IsSet($PreviousReportingLevel)) {
			error_reporting($PreviousReportingLevel);
		}

		return $return;
	}

	function AddToDefault($UserID, $ElementID) {
		#
		# Add an item to all default watch lists
		#

		#
		# The subselect ensures the user can only add things to their
		# own watch list and avoid duplicate key problems.
		#
		# Looking at this today, I'd use an ON CONFLICT clause.
		# dvl - 2023-04-01
		#
		$sql = '
INSERT INTO watch_list_element 
select id, $1
  from watch_list 
 where in_service = TRUE 
   and user_id = $2
   and not exists (
    SELECT *
      FROM watch_list_element
     WHERE watch_list_element.watch_list_id = watch_list.id
       AND watch_list_element.element_id    = $1)';

		if ($this->Debug) echo "<pre>$sql</pre>";
		$result = pg_query_params($this->dbh, $sql, array($ElementID, $UserID));
		if ($result) {
			$return = 1;
		} else {
			$return = -1;
		}

		return $return;
	}

	function PopulateValues($myrow) {
		#
		# call Fetch first.
		# then call this function N times, where N is the number
		# returned by Fetch.
		#

		$this->watch_list_id    = $myrow["watch_list_id"];
		$this->element_id       = $myrow["element_id"];

		$this->watch_list_count = $myrow["watch_list_count"];
		$this->user_id          = $myrow["user_id"];
	}
}

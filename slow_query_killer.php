<?
/**
 * Kill slow running queries on a MySQL database from certain users / tables.
 *
 * Run at your own risk.
 * 
 * @author Russell Smith <russell.smith@ukd1.co.uk>
 * @copyright UKD1 Limited 2009
 * @license Attribution-Share Alike 2.0 UK: England & Wales License
 * @see http://creativecommons.org/licenses/by-sa/2.0/uk/
 */

/**
 * DB host
 *
 */
define('DB_HOST', 'localhost');

/**
 * DB username - must have rights to kill processes
 *
 */
define('DB_USER', 'root');

/**
 * DB password
 *
 */
define('DB_PASS', 'your root password here');




/**
 * Array of rules - which are Kill_Rule objects
 *
 */
$rules = array(
	new Kill_Rule('user', 'database', 15),
//	new Kill_Rule('user2', 'database', 15),
	);


// remove these lines before using
exit();
	
/**
 * You shouldn't need to edit below this...but do read it before you run.
 */
	
	

/**
 * Kill rule class
 *
 */
class Kill_Rule {
	/**
	 * User running the query
	 *
	 * @var string
	 */
	public $user;
	
	/**
	 * Db the query is running on 
	 *
	 * @var string
	 */
	public $db;
	
	/**
	 * After this many seconds, kill the query
	 *
	 * @var int
	 */
	public $kill_after_seconds;
	
	
	/**
	 * Just store the passed data in the object
	 *
	 * @param string $user
	 * @param string $host
	 * @param int $kill_after_seconds
	 */
	public function __construct ($user = null, $db = null, $kill_after_seconds = null) {
		$this->user = $user;
		$this->db = $db;
		
		$this->kill_after_seconds = $kill_after_seconds;
	}
}
	
	
// connect to the db	
mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());

// get the query
$processes = mysql_query('SHOW PROCESSLIST') or die(mysql_error());

while ($process = mysql_fetch_object($processes)) {
	// loop through the rules
	foreach ($rules as $rule) {
		// does the process match - user & db name? Is it over the kill time?
		if ($rule->user == $process->User && $rule->db == $process->db && $rule->kill_after_seconds > 0 && $process->Time > $rule->kill_after_seconds) {
			// yes, kill the process
			mysql_query('KILL ' . $process->Id) or die(mysql_error());
		}
	}
}

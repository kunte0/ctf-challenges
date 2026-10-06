<?php

include 'html/util/Database.php';


// users: id/name/password/winner
$userSchema = "CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, winner INTEGER);";
// ideas: id/owner/text/pad/shared
$ideaSchema = "CREATE TABLE ideas (id INTEGER PRIMARY KEY, owner INTEGER, text TEXT, pad INTEGER, shared INTEGER);";
// flag: id/flag
$flagSchema = "CREATE TABLE flag (id INTEGER PRIMARY KEY, flag TEXT);";



$USERDB = Database::buildSQLite('IDeaShareUsers.sqlite3');
$IDEADB = Database::buildSQLite('IDeaShareIDeas.sqlite3');
$FLAGDB = Database::buildSQLite('IDeaShareFlags.sqlite3');



echo $USERDB->isConnected();
echo $IDEADB->isConnected();
echo $FLAGDB->isConnected();

$USERDB->execute($userSchema);
echo "Created IDeaShareUsers\n";

$USERDB->put('INSERT into users (username, password, winner) VALUES (?,?,?)',
    'admin',
    hash('sha256', '7ecace2c782ae60271876fefef0992dcleldc335bf361730b4701437d66af52e843'),
    0
);
echo "Added Admin user\n";

$USERDB->put('INSERT into users (username, password, winner) VALUES (?,?,?)',
    'BOT7000',
    hash('sha256', 'f26aaeab1ef4460bc422f536b94'),
    0
);

echo "Added BOT7000 user\n password = f26aaeab1ef4460bc422f536b94\n";

$IDEADB->execute($ideaSchema);
echo "Created IDeaShareIdeas\n";


$FLAGDB->execute($flagSchema);
echo "Created IDeaShareFlags\n";

$FLAGDB->put('INSERT into flag (flag) VALUES (?)',
    'flag{wow_you_tricked_patrick_G00D?JOB}'
);

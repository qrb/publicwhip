<?php require_once "../common.inc";
# $Id: archive.php,v 1.8 2005/01/15 20:38:11 frabcus Exp $

# The Public Whip, Copyright (C) 2003 Francis Irving and Julian Todd
# This is free software, and you are welcome to redistribute it under
# certain conditions.  However, it comes with ABSOLUTELY NO WARRANTY.
# For details see the file LICENSE.html in the top level of the source.
$issue = intval($_GET["issue"]);
$extra = intval($_GET["extra"]);

function newsletter_title($newsletter)
{
	$handle = fopen($newsletter, 'r');
	$line = fgets($handle);
    fclose($handle);
    return str_replace("Subject: ", "", $line);
}
function newsletter_date($newsletter)
{
	$handle = fopen($newsletter, 'r');
	$dummy = fgets($handle);
	$line = fgets($handle);
    fclose($handle);
    return strtotime(str_replace("Date: ", "", $line));
}


function render_newsletter($newsletter)
{
	$handle = fopen($newsletter, 'r');
    $c = 0;
	while ($line = fgets($handle))
	{
        $line = preg_replace("/\b(\S+\@\S+)\b/is", "<a href=\"mailto:\\1\">\\1</a>", $line);
        $line = preg_replace("/(\s|^)(http:\/\/\S+)(\s)/is", "\\1<a href=\"\\2\">\\2</a>\\3", $line);

        $c++;
        if ($c >= 4)
            print $line . "<br>";
	}
	fclose($handle);
}

$title = "Newsletter Archive"; 
if ($issue != 0)
{
    $title = newsletter_title("issue" . $issue . ".txt") . " - " .
        date("j M Y", newsletter_date("issue" . $issue . ".txt"));
}
else if ($extra != 0)
{
    $title = newsletter_title("extra" . $extra . ".txt") . " - " .
        date("j M Y", newsletter_date("extra" . $extra . ".txt"));
}
include "../header.inc";

if ($issue == 0 and $extra == 0)
{
?><p>This is the archive of old issues of the Public Whip newsletter.  At most
every month we'll email you with news, articles and comment about the
project.  Occasionally we will send an extra small topical newsletter.
<p>
<?
    if (!user_isloggedin())  {
        ?><a href="../account/register.php">Sign up now!</a>  Get the newsletter by email. It's free!<p><?
    } else {
        ?><a href="../account/settings.php">Change your newsletter subscription setting</a>.<p><?
    }

    $dh = opendir(".");
    $filenames = array();
    while (false !== ($filename = readdir($dh))) {
        if (preg_match("/^(issue|extra)(.*)\.txt$/", $filename, $matches))
            array_push($filenames, $filename);
    }
    function newslettercompare($a, $b) {
        return newsletter_date($a) < newsletter_date($b);
    }
    usort($filenames, "newslettercompare");
    print "<table>";
    foreach ($filenames as $filename) {
        if (preg_match("/^(issue|extra)(.*)\.txt$/", $filename, $matches))
        {
            print "<tr><td>";
            print date("j M Y", newsletter_date($filename));
            print "</td><td>";
            print "<a href=\"archive.php?" . $matches[1] . "=" . $matches[2] . "\">";
            print newsletter_title($filename);
            print "</a>";
            print "</td></tr>";
        }
    }
    print "</table>";
    print "<p><a href=\"old.php\">Older site news</a><?";
   /*3 December 2003
   31 October 2003 */

}
else
{
    print "<p><a href=\"archive.php\">Full list of old newsletter issues here</a>";
    if (!user_isloggedin())  {
        print "<br><a href=\"../account/register.php\">Subscribe to the newsletter for free!</a> ";
    } else {
        print "<br><a href=\"../account/settings.php\">Change your newsletter subscription setting.</a> ";
    }

    print "</p><hr><p>";
    if ($extra != 0)
        render_newsletter("extra" . $extra . ".txt");
    else
        render_newsletter("issue" . $issue . ".txt");
}

?>

<?php include "../footer.inc" ?>


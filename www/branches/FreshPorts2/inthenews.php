<?
   # $Id: inthenews.php,v 1.1.2.2 2002-01-05 21:24:28 dan Exp $
   #
   # Copyright (c) 1998-2001 DVL Software Limited

   require("./include/common.php");
   require("./include/freshports.php");
   require("./include/databaselogin.php");
   require("./include/getvalues.php");


   freshports_Start("In The News",
               "freshports - new ports, applications",
               "FreeBSD, index, applications, ports");

?>
<table width="<? echo $TableWidth ?>">
<tr><td valign="top">
<font size="+2">in the news</font> 
<p>This page is just a place for me to record the <? echo $FreshPortsTitle; ?> articles which appear
on other sites.  Links are recorded in reverse chronological order (i.e. newest first).  If you spot an article which 
is not listed here, please <a href="http://freshports.org/phorum/list.php?f=3">let me know</a>.
</p>
<p>
BSD Today - <a href="http://www.bsdtoday.com/2000/May/News146.html">Keeping track of your favorite ports</a>
</p>

<p>
slashdot - <a href="http://slashdot.org/article.pl?sid=00/05/10/1014226">BSD: FreshPorts</a>
</p>

Daily Daemon News - <a href="http://daily.daemonnews.org/view_story.php3?story_id=889"><? echo $FreshPortsTitle; ?> site announncement</a>

</td>
  <td valign="top" width="*">
    <? include("./include/side-bars.php") ?>
 </td>
</tr>
</table>
<? include("./include/footer.php") ?>
</body>
</html>

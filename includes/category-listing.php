<div id="sidebar" class="<?php echo $mcat_id; ?>">
    <div class="sidebar-holder">
        <div class="sidebar-frame">
            <h2><?php echo ($mcatname) ? strtoupper($mcatname) : "All Categories" ; ?></h2>
            <ul class="sidenav">
                <?php if ($mcat_id) {
                    $categories = mysql_query("SELECT categories.*, mcat.mcat_name, mcat.safe_name as mcat_safe_name FROM categories Inner Join mcat On (categories.mcat_id = mcat.mcat_id) WHERE categories.mcat_id='$mcat_id' ORDER BY categories.name ASC;");
                    while($row=mysql_fetch_array($categories)){?>
                        <li><a <?php echo ($row['id'] == $cat_id) ? "class=\"active\"" : "" ; ?> href="/services/<?php echo urlencode(str_replace('/', '[and]', $row['mcat_safe_name'])); ?>/<?php echo urlencode(str_replace('/', '[and]', $row['safe_name'])); ?>/<?php echo $_SESSION['zip']; ?>"><?php echo $row['name']; ?></a></li>
                    <?php } 
                }else{
                    $categories = mysql_query("SELECT * FROM mcat ORDER BY mcat_name ASC;");
                    while($row=mysql_fetch_array($categories)){?>
                        <li><a href="/services/<?php echo urlencode(str_replace('/', '[and]', $row['safe_name'])); ?>/<?php echo $_SESSION['zip']; ?>"><?php echo $row['mcat_name']; ?></a></li>
                    <?php } 
                }?>
            </ul>
            <br style="clear: both;"/>
            &nbsp;
        </div>
    </div>
</div>
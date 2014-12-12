<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2>Munin Graphs</h2>
	<?php 
		$this->NewTop;
	?>
	<table cellspacing="0" cellpadding="10" align="center" width="50%">
	<?php while ($this->loop('Rows')) : ?>
		<tr><td>
		<a href='<?php $this->_Munin_Url; ?>/<?php $this->_Domain; ?>/<?php $this->_Dname; ?>/<?php $this->_Graph_name; ?>.html' target='_blank'>
		<img src='index.php?module=image&img=muningraph&object_id=<?php $this->_Object_Id; ?>&server_id=<?php $this->_Server_Id; ?>&graph=$<?php $this->_Graph_Name; ?>' alt='(graph <?php $this->_Graph_Name; ?> on server <?php $this->_Server_Id; ?>)' title='(graph <?php $this->_Graph_Name; ?> on server <?php $this->_Server_Id; ?>)'></a></td>
		<td>	
		<?php 
			$this->getH('GetOpLink', array(array ('op' => 'del', 'server_id' => $this->_Server_Id,  'graph' => $graph_name), '', 'Cut', 'Unlink graph', 'need-confirmation'));
		?>
		&nbsp; &nbsp;<?php 	$this->Caption; ?>	
		</td></tr>
	<?php endwhile ?>
	</table>
	<?php
		$this->NewBottom;
	 ?>
	 </div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>
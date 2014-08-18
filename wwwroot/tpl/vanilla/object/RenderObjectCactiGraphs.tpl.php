<?php if (defined("RS_TPL")) {?>
	<div class=portlet><h2>Cacti Graphs</h2>
	<?php 
		$this->NewTop;
	?>
	<table cellspacing="0" cellpadding="10" align="center" width="50%">
	<?php while ($this->loop('RowsTR')) : ?>
		<tr><td>
		<a href='<?php $this->_Cacti_Url; ?>graph.php?action=view&local_graph_id=<?php $this->_Graph_Id; ?>&rra_id=all' target='_blank'>
		<img src='index.php?module=image&img=cactigraph&object_id=<?php $this->_Object_Id; ?>&server_id=<?php $this->_Server_Id; ?>&graph_id=<?php $this->_Graph_Id; ?>' alt='(graph <?php $this->_Graph_Id; ?> on server <?php $this->_Server_Id; ?>)' title='(graph <?php $this->_Graph_Id; ?> on server <?php $this->_Server_Id; ?>)'></a></td><td>
		<td>	
		<?php if($this->is('Permitted', TRUE)){
			$this->getH('GetOpLink', array(array ('op' => 'del', 'server_id' => $this->_Server_Id,  'graph' => $graph_name), '', 'Cut', 'Unlink graph', 'need-confirmation'));
		}
		?>
		&nbsp; &nbsp;<?php 	$this->Caption; ?>	
		</td></tr>
	<?php endwhile ?>
	</table>
	<?php $this->NewBottom; ?>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>
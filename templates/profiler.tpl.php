<?php
/**
 * @var array $profiler_checks;
 * */
?>
<style>
	#profiler {
		position: fixed;
		left: 20px;
		bottom: 20px;
		background: grey;
		color: white;
		z-index:1000000;
	}
</style>
<table id="profiler">
	<th>Description</th>
	<th>Time to point (mсs)</th>
	<th>Time from start (mсs)</th>
	<?php foreach( $profiler_checks as $row ):  ?>
		<tr>
			<td><?php echo $row['description']; ?></td>
			<td><?php echo $row['time_from_last_check']; ?></td>
			<td><?php echo $row['time_from_start']; ?></td>
		</tr>
	<?php endforeach; ?>
</table>
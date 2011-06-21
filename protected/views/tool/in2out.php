<?php
$this->breadcrumbs=array(
	'Tool',
);?>


<div class="form">

<?php echo CHtml::beginForm($action='', $method='post'); ?>

	<div class="row">
		<?php 
		if(isset($in)){
			echo CHtml::textArea('in', $in, array('style'=>'float:left; height: 350px;width: 450px;'));
		}else{
			echo CHtml::textArea('in', '', array('style'=>'float:left; height: 350px;width: 450px;'));
		}
		?>
	</div>

	<div class="row">
		<?php 
		if(isset($out)){
			echo CHtml::textArea('out', $out, array('style'=>'float:right; height: 350px;width: 450px;'));
		}else{
			echo CHtml::textArea('out', '', array('style'=>'float:right; height: 350px;width: 450px;'));
		}
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::SubmitButton($label='RUN'); ?>
	</div>

<?php echo CHtml::endForm(); ?>

</div><!-- form -->
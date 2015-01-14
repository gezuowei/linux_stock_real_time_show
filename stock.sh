#! /bin/bash

function add(){
	php lib/operate.php add $1
}

function del(){
	php lib/operate.php del $1
}
function show(){
	php view/show.php
}

command=$1;

case $command in
	add)
	add $2
	;;

	del)
	del $2
	;;

	*)
	show
	;;

esac

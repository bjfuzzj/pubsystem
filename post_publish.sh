pubnum=`ps -ef|grep "post_publish.php"|grep -v grep|wc -l`

if [ $pubnum -eq 0 ]
then
/usr/bin/php post_publish.php  www
fi

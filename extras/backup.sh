##############################################################
#####################Usage####################################
#Prints out some short manual on the usage of the script     #
#                                                            #
#                                                            #
#                                                            #
#                                                            #
##############################################################
usage()
{

echo "This script will do a database backup and set up cron to repeat the process
every day at midnight".
cat << EOF
usage: $0 options

OPTIONS:
   -h      Set the mysql host
   -u      Set the mysql user
   -p      set the mysql password
   -d      sets the mysql database name to be backed up
   -b      Sets the path to the directory where the back up will be stored
   -v      gives this instructions
EOF
}
HOST="localhost"
USERNAME="root"
PASSWORD=""
DATABASE="sizzle"
#BACKUPFOLDERPATH="/home/fs/kkarithi/Documents/mysql"
while getopts " h: u: p:d:b:f:v" opt; do
  case $opt in
    h)
      #unset HOST
      #"${HOST:-$OPTARG}"
      HOST=$OPTARG
      
      ;;
     u)
       USERNAME=$OPTARG
       ;;
     p)
       PASSWORD=$OPTARG
       ;;
     d)
       DATABASE=$OPTARG
       ;;
     b)
       BACKUPFOLDERPATH=$OPTARG
        echo "$BACKUPFOLDERPATH"
	if [ -d $BACKUPFOLDERPATH ]; then
	echo "Existing folder  $BACKUPFOLDERPATH ,everything in it will be cleared"
	else
	BACKUPFOLDERPATH="/home/fs/kkarithi/Documents/mysql"
	fi

       ;;
     f)
       #readfromfile
	echo "This option is not functional at the moment"
       ;;
    \?)
      usage
      exit 1
      ;;
    v)
      usage
      exit 1
      ;;
  esac
done

##-----------------------------readfromfile----------------##
#
#reads configurations from a text file that can be used to  #
#access the server as well as the path to the backup.        #
#
#-----------------------------------------------------------#
readfromfile()
{
#CONFIGFILE=
echo "Not yet implemented option"
}
#Command to run the mysql dump

MYSQLDUMP="mysqldump"

##Path to the backup file##

#BAK="/home/slowcoach/backup/mysql"

#gzip command to zip the output file
GZIP="gzip"
###Time of the backup###
NOW=$(date +"%d-%m-%Y")
##create the backup folder path or clean it all together if it has content
[ ! -d $BACKUPFOLDERPATH ] && mkdir -p $BACKUPFOLDERPATH || /bin/rm -f $BACKUPFOLDERPATH/*.gz
##The file as which the database is to be backed up##

 FILE=$BACKUPFOLDERPATH/$DATABASE.$NOW-$(date +"%T").gz
#Actual command to perform a dump of the database as well as
#compressing the file into a gzip
echo -e "dumpimg mysql with the following configuration: \n HOST: '$HOST' \n\n DATABASE NAME : '$DATABASE'\n\n MYSQL USER : '$USERNAME' \n\n MYSQL PASSWORD : '$PASSWORD' \n\n BACK UP FILE : '$FILE'" 
 $MYSQLDUMP -u $USERNAME -h $HOST -p$PASSWORD $DATABASE| $GZIP -9 > $FILE
#done

echo 'done'
#the entry in the crontab to run this automatically
#@midnight /home/slowcoach/Finland/works/code/backups/gz_sample.sh >/dev/null 2>&1

#!/bin/bash

function prompter ( ) {
   read -p "$@"
}

function die ( )  {
    echo "$@"
    # replace the dummy .htaccess with my actual .htacess before exiting
    cp -f data/cache/htaccess/new/.htaccess public/.htaccess
    exit 1
}

echo "Initializing..."

cd ..
# backup my .htacess
cp -f public/.htaccess data/cache/htaccess/new/.htaccess
# copy over the dummy .htacess so git won't detect any modifications
cp -f data/cache/htaccess/original/.htaccess public/.htaccess

prompter "Select an operation (push / pull / exit)? "

if [ "$REPLY" == "pull" ]; then

    git fetch origin

    prompter "Continue to merge (y/n)? "

    if [ "$REPLY" == "n" ]; then
        die "Exiting..."
    elif [ "$REPLY" == "y" ]; then
        git merge origin/master
	echo "All done!"
    fi

elif [ "$REPLY" == "push" ]; then

    git status

    prompter "Continue to commit (git commit -a) (y/n)? "

    if [ "$REPLY" == "n" ]; then
        die "Exiting..."
    elif [ "$REPLY" == "y" ]; then
        prompter "Please enter a commit message: "
        first_time=true
        # force a commit message
        while [ "$REPLY" == "" ]
        do
            first_time=false
            prompter "Please enter a commit message. Enter 'damn you' to exit: "
        done
        #if [ "$REPLY" == "damn you" -a !$first_time ]; then
        if [[ "$REPLY" == "damn you" && !$first_time ]]; then
            die "Exiting..."
        else
            message="$REPLY"
        fi

        git commit -a -m "$message"
        git status

        prompter "Continue to push (y/n)? "

        if [ "$REPLY" == "n" ]; then
            die "Exiting..."
        elif [ "$REPLY" == "y" ]; then
            git push
	    echo "All done!"
        fi
    fi
elif [ "$REPLY" == "exit" ]; then
    die "Exiting..."
else
    die "Wrong! Exiting..."
fi

die "Exiting..."

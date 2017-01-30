#!/bin/sh

tmux start-server
tmux new-session -d -s docker -n code
tmux new-window -tdocker:1 -n code
tmux split-window -tdocker:1 -p 50 -h

tmux send-keys -tdocker:1 'cd ~/projects/others/pasquinis/themis; clear' C-m

tmux select-window -tdocker:1
tmux attach-session -d -tdocker

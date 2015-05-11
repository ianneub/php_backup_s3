#!/bin/sh
killall prltoolsd 2>/dev/null
ntpdate -u 0.pool.ntp.org

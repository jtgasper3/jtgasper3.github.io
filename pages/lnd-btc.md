---
layout: page
title: Setting up a Lightning Node on a Raspberry Pi
---

## Prerequisites

### Add a new users

1. Create a working user account:

   ```sh
   $ sudo adduser <username>
   Adding user `<username>' ...
   Adding new group `<username>' (1001) ...
   Adding new user `<username>' (1001) with group `<username>' ...
   Creating home directory `/home/<username>' ...
   Copying files from `/etc/skel' ...
   Enter new UNIX password:
   Retype new UNIX password:
   passwd: password updated successfully
   Changing the user information for <username>
   Enter the new value, or press ENTER for the default
       Full Name []:
       Room Number []:
       Work Phone []:
       Home Phone []:
       Other []:
   Is the information correct? [Y/n]
   ```

1. Grant sudo rights:

   ```bash
   sudo usermod -aG sudo <username>
   ```

1. Disconnect from the default account:

   ```bash
   exit
   ```

1. Copy the ssh pub key to the new (working) account:

   ```bash
   $ ssh-copy-id <username>@<ip-address>
   /usr/local/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
   /usr/local/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
   <username>@<ip-address>'s password:

   Number of key(s) added:        1

   Now try logging into the machine, with:   "ssh '<username>@<ip-address>'"
   and check to make sure that only the key(s) you wanted were added.
   ```

1. Reconnect as the new working user account:

   ```bash
   ssh <username>@<ip-address>
   ```

1. Remove the default account:

   ```bash
   $ sudo deluser --remove-home pi
   Looking for files to backup/remove ...
   Removing user `pi' ...
   Warning: group `pi' has no more members.
   Done.
   ```

1. Create the `bitcoin` user/service account:

   ```bash
   $ sudo adduser bitcoin
   Adding user `bitcoin' ...
   Adding new group `bitcoin' (1000) ...
   Adding new user `bitcoin' (1000) with group `bitcoin' ...
   Creating home directory `/home/bitcoin' ...
   Copying files from `/etc/skel' ...
   Enter new UNIX password:
   Retype new UNIX password:
   passwd: password updated successfully
   Changing the user information for bitcoin
   Enter the new value, or press ENTER for the default
       Full Name []:
       Room Number []:
       Work Phone []:
       Home Phone []:
       Other []:
   Is the information correct? [Y/n]
   ```

1. Create the `lightning` user/service account:
   ```sh
   $ sudo adduser lightning
   Adding user `lightning' ...
   Adding new group `lightning' (1002) ...
   Adding new user `lightning' (1002) with group `lightning' ...
   Creating home directory `/home/lightning' ...
   Copying files from `/etc/skel' ...
   Enter new UNIX password:
   Retype new UNIX password:
   passwd: password updated successfully
   Changing the user information for lightning
   Enter the new value, or press ENTER for the default
       Full Name []:
       Room Number []:
       Work Phone []:
       Home Phone []:
       Other []:
   Is the information correct? [Y/n]
   ```

### USB Drive Setup

Setup the drive for auto mounting after restart by adding the following with `sudo vim /etc/fstab`:

```
/dev/sda1             /mnt/hdd        ext4    defaults      0      0
```

## Setting up a Full Bitcoin Node

1. Prep the bitcoin data directories:

   ```sh
   sudo mkdir /mnt/hdd/bitcoin
   sudo chown bitcoin:bitcoin /mnt/hdd/bitcoin
   sudo su bitcoin
   ln -s /mnt/hdd/bitcoin /home/bitcoin/.bitcoin
   ```

1. Populate with the following `vim /home/bitcoin/.bitcoin/bitcoin.conf`:

   ```ini
   # Bitcoind options
   server=1
   daemon=1
   txindex=0
   disablewallet=1
   prune=550
   # Connection settings
   rpcuser=<rpc-username>
   rpcpassword=<rpc-password>
   onlynet=ipv4
   zmqpubrawblock=tcp://127.0.0.1:29000
   zmqpubrawtx=tcp://127.0.0.1:29001
   # Raspberry Pi optimizations
   dbcache=100
   maxorphantx=10
   maxmempool=50
   maxconnections=40
   maxuploadtarget=5000
   ```

1. Exit the bitcoin user:

   ```sh
   exit
   ```

1. Install (Update the version from https://bitcoincore.org/bin/):

   ```sh
   BITCOIND_VERSION=0.19.0.1
   ARCH=arm-linux-gnueabihf
   BITCOIND_ARCHIVE=bitcoin-${BITCOIND_VERSION}-${ARCH}.tar.gz
   cd /tmp \
   && wget https://bitcoincore.org/bin/bitcoin-core-${BITCOIND_VERSION}/${BITCOIND_ARCHIVE} \
   && wget https://bitcoincore.org/bin/bitcoin-core-${BITCOIND_VERSION}/SHA256SUMS.asc \
   && wget https://bitcoin.org/laanwj-releases.asc \
   && SHA256=`grep "${BITCOIND_ARCHIVE}" SHA256SUMS.asc | awk '{print $1}'` \
   && echo $SHA256 \
   && echo "$SHA256 ${BITCOIND_ARCHIVE}" | sha256sum -c - \
   && gpg --import ./laanwj-releases.asc \
   && gpg --verify SHA256SUMS.asc \
   && tar -xzf ${BITCOIND_ARCHIVE} \
   && sudo install -m 0755 -o root -g root -t /usr/local/bin bitcoin-${BITCOIND_VERSION}/bin/* \
   && rm -rf /tmp/bitcoin-core-${BITCOIND_VERSION}/${BITCOIND_ARCHIVE} /tmp/bitcoin-core-${BITCOIND_VERSION}/SHA256SUMS.asc \
   && bitcoind --version
   ```

1. Create a new service with the following `sudo vim /etc/systemd/system/bitcoind.service`:

   ```ini
   [Unit]
   Description=Bitcoin daemon
   Wants=getpublicip.service
   After=getpublicip.service

   [Service]
   ExecStartPre=/bin/sh -c 'sleep 30'
   ExecStart=/usr/local/bin/bitcoind -daemon -conf=/home/bitcoin/.bitcoin/bitcoin.conf -pid=/home/bitcoin/.bitcoin/bitcoind.pid
   PIDFile=/home/bitcoin/.bitcoin/bitcoind.pid
   User=bitcoin
   Group=bitcoin
   Type=forking
   KillMode=process
   Restart=always
   TimeoutSec=120
   RestartSec=30

   [Install]
   WantedBy=multi-user.target
   ```

1. Enable and start the new service:

   ```sh
   sudo systemctl enable bitcoind.service
   sudo systemctl start bitcoind.service
   sudo systemctl status bitcoind.service
   ```

1. Check the bitcoin service:

   ```sh
   $ bitcoin-cli getblockchaininfo | grep 'verificationprogress\|size_on_disk\|mediantime\|blocks\|headers'
   "blocks": 562385,
   "headers": 562385,
   "mediantime": 1549761244,
   "verificationprogress": 0.9999997986853417,
   "size_on_disk": 26188591779,
   ```

## Setting up the Lightning Node

1. Prep the bitcoin data directories:

   ```sh
   sudo mkdir /mnt/hdd/lnd
   sudo chown lightning:lightning /mnt/hdd/lnd
   sudo su lightning
   ln -s /mnt/hdd/lnd /home/lightning/.lnd
   ```

1. Populate with the following `$ vim /home/lightning/.lnd/lnd.conf`:

   ```ini
   # RaspiBolt: lnd configuration

   [Application Options]
   debuglevel=info
   maxpendingchannels=5
   alias=PI-LND
   color=#68F442

   # Your router must support and enable UPnP, otherwise delete this line
   #nat=true

   [Bitcoin]
   bitcoin.active=1

   # enable either testnet or mainnet
   #bitcoin.testnet=1
   bitcoin.mainnet=1

   bitcoin.node=bitcoind

   [Bitcoind]
   bitcoind.rpcuser=<rpc-username>
   bitcoind.rpcpass=<rpc-password>
   bitcoind.zmqpubrawblock=tcp://127.0.0.1:29000
   bitcoind.zmqpubrawtx=tcp://127.0.0.1:29001

   [autopilot]
   autopilot.active=1
   autopilot.maxchannels=5
   autopilot.allocation=0.6
   ```

1. Install (Update the version from https://github.com/lightningnetwork/lnd/releases)

   ```sh
   LND_VERSION=v0.8.2-beta-rc2
   ARCH=linux-armv7
   LND_ARCHIVE=lnd-${ARCH}-${LND_VERSION}.tar.gz
   cd /tmp \
       && wget -q https://github.com/lightningnetwork/lnd/releases/download/${LND_VERSION}/${LND_ARCHIVE} \
       && wget -q https://github.com/lightningnetwork/lnd/releases/download/${LND_VERSION}/manifest-${LND_VERSION}.txt \
       && wget -q https://github.com/lightningnetwork/lnd/releases/download/${LND_VERSION}/manifest-${LND_VERSION}.txt.sig \
       && wget -q https://keybase.io/roasbeef/pgp_keys.asc \
       && SHA256=`grep "${LND_ARCHIVE}" manifest-${LND_VERSION}.txt | awk '{print $1}'` \
       && echo $SHA256 \
       && sha256sum ${LND_ARCHIVE} \
       && echo "$SHA256 ${LND_ARCHIVE}" | sha256sum -c - \
       && gpg --import ./pgp_keys.asc \
       && gpg --verify manifest-${LND_VERSION}.txt.sig \
       && tar -xzf ${LND_ARCHIVE} \
       && sudo install -m 0755 -o root -g root -t /usr/local/bin lnd-${ARCH}-${LND_VERSION}/* \
       && rm -rf /tmp/${LND_ARCHIVE}* /tmp/manifest-${LND_VERSION}* \
       && lnd --version
   ```

1. Verify the configuration, run:

   ```sh
   lnd
   ```

1. In a separate ssh instance with the working account, create a new LND wallet:

   ```sh
   lncli create
   ```

   Verify everything is connecting.

1. Create a new service with the following `sudo vim /etc/systemd/system/lnd.service`:

   ```ini
   [Unit]
   Description=LND Lightning Daemon
   Wants=bitcoind.service
   After=bitcoind.service

   [Service]
   ExecStart=/usr/local/bin/lnd
   PIDFile=/home/lightning/.lnd/lnd.pid
   User=lightning
   Group=lightning
   LimitNOFILE=128000
   Type=simple
   KillMode=process
   TimeoutSec=180
   Restart=always
   RestartSec=60

   [Install]
   WantedBy=multi-user.target
   ```

1. Enable and start the new service:

   ```sh
   sudo systemctl enable lnd
   sudo systemctl start lnd
   systemctl status lnd
   ```

1. Re-run the following to unlock the wallet:

   ```sh
   lncli unlock
   ```

1. Check the logs:

   ```sh
   sudo journalctl -f -u lnd
   ```

## References

- https://medium.com/coinmonks/bitcoin-lightning-network-run-your-node-at-home-for-fun-and-no-profit-da5b61be2ba9
- https://github.com/Stadicus/guides/blob/master/raspibolt/raspibolt_40_lnd.md
- https://github.com/Stadicus/guides/blob/master/raspibolt/raspibolt_50_mainnet.md#lnd-in-action
- https://www.digitalocean.com/community/tutorials/how-to-add-and-delete-users-on-debian-8
- https://www.raspberrypi.org/documentation/remote-access/ssh/passwordless.md
- https://askubuntu.com/questions/929142/timecapsule-connection-via-ehternet

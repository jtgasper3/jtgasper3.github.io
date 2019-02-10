---
layout: page
---

# Setting up a Lightning node on a raspberrypi

## Prerequisites

### Add a new users

    ```bash
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

    ```bash
    $ sudo usermod -aG sudo <username>
    ```

    ```bash
    $ exit
    ```

    ```bash
    ssh-copy-id <username>@<ip-address>
    /usr/local/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
    /usr/local/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
    <username>@<ip-address>'s password:

    Number of key(s) added:        1

    Now try logging into the machine, with:   "ssh '<username>@<ip-address>'"
    and check to make sure that only the key(s) you wanted were added.
    ```

    ```bash
    ssh <username>@<ip-address>
    ```

    ```bash
    sudo deluser --remove-home pi
    Looking for files to backup/remove ...
    Removing user `pi' ...
    Warning: group `pi' has no more members.
    Done.
    ```

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

### USB Drive Setup

```bash
$ sudo vim /etc/fstab
/dev/sda1             /mnt/hdd        ext4    defaults      0      0
```


```
$ sudo mkdir /mnt/hdd/bitcoin
$ sudo chown bitcoin:bitcoin /mnt/hdd/bitcoin
```

```bash
sudo su bitcoin
ln -s /mnt/hdd/bitcoin /home/bitcoin/.bitcoin
```

```vim /home/bitcoin/.bitcoin/bitcoin.conf:
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

```
exit
```

## Setting up a Full Bitcoin Node

Update the version from https://bitcoincore.org/bin/:

    ```bash 
    BITCOIND_VERSION=0.17.0.1
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
    && rm -rf /tmp/* \
    && bitcoind --version
    ```

sudo vim /etc/systemd/system/bitcoind.service


```
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


```
sudo systemctl enable bitcoind.service
sudo systemctl start bitcoind.service
sudo systemctl status bitcoind.service
```

```
$ bitcoin-cli getblockchaininfo | grep 'verificationprogress\|size_on_disk\|mediantime\|blocks\|headers'
  "blocks": 562385,
  "headers": 562385,
  "mediantime": 1549761244,
  "verificationprogress": 0.9999997986853417,
  "size_on_disk": 26188591779,
```

## Setting up the Lightning Node

```bash
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

```
$ sudo mkdir /mnt/hdd/lnd
$ sudo chown lightning:lightning /mnt/hdd/lnd
```

```bash
$ sudo su lightning
$ ln -s /mnt/hdd/lnd /home/lightning/.lnd
$ vim /home/lightning/.lnd/lnd.conf
```

```
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

```
LND_VERSION=v0.5.1-beta
ARCH=linux-armv7
LND_ARCHIVE=lnd-${ARCH}-${LND_VERSION}.tar.gz
https://github.com/lightningnetwork/lnd/releases/download/v0.5.1-beta/lnd-linux-armv7-v0.5.1-beta.tar.gz
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
    && rm -rf /tmp/* \
    && lnd --version
```

Run:

```
lnd
```

In a seperate ssh instance:

```
lncli create
```

Verify everything is connecting.

```
sudo vim /etc/systemd/system/lnd.service
```

```
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

```
sudo systemctl enable lnd
sudo systemctl start lnd
systemctl status lnd
```

Re-run:

```
lncli unlock
```

Check the logs:

```
sudo journalctl -f -u lnd
```

## References
- https://medium.com/coinmonks/bitcoin-lightning-network-run-your-node-at-home-for-fun-and-no-profit-da5b61be2ba9
- https://github.com/Stadicus/guides/blob/master/raspibolt/raspibolt_40_lnd.md
- https://github.com/Stadicus/guides/blob/master/raspibolt/raspibolt_50_mainnet.md#lnd-in-action
- https://www.digitalocean.com/community/tutorials/how-to-add-and-delete-users-on-debian-8
- https://www.raspberrypi.org/documentation/remote-access/ssh/passwordless.md
- https://askubuntu.com/questions/929142/timecapsule-connection-via-ehternet

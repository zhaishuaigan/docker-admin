# 更换aliyun软件源
echo 'deb http://mirrors.aliyun.com/ubuntu/ xenial main restricted universe multiverse' > /etc/apt/sources.list
echo 'deb http://mirrors.aliyun.com/ubuntu/ xenial-security main restricted universe multiverse' >> /etc/apt/sources.list
echo 'deb http://mirrors.aliyun.com/ubuntu/ xenial-updates main restricted universe multiverse' >> /etc/apt/sources.list
echo 'deb http://mirrors.aliyun.com/ubuntu/ xenial-proposed main restricted universe multiverse' >> /etc/apt/sources.list
echo 'deb http://mirrors.aliyun.com/ubuntu/ xenial-backports main restricted universe multiverse' >> /etc/apt/sources.list
apt-get update

# 安装docker
apt-get -y install apt-transport-https ca-certificates curl software-properties-common
curl -fsSL http://mirrors.aliyun.com/docker-ce/linux/ubuntu/gpg | sudo apt-key add -
add-apt-repository "deb [arch=amd64] http://mirrors.aliyun.com/docker-ce/linux/ubuntu $(lsb_release -cs) stable"
apt-get -y update
apt-get -y install docker-ce docker-compose dos2unix
# find /data/docker/update/ -name "*" | xargs dos2unix


# 使用aliyun加速docker镜像
sudo mkdir -p /etc/docker
sudo echo '{' > /etc/docker/daemon.json
sudo echo '    "registry-mirrors": ["https://kag9wqej.mirror.aliyuncs.com"]' >> /etc/docker/daemon.json
sudo echo '}' >> /etc/docker/daemon.json
sudo systemctl daemon-reload
sudo systemctl restart docker

echo 'finish' > /data/docker/install.lock

echo 'if [ -f /data/docker/root.sh ]; then' >> /root/.bashrc
echo '    source /data/docker/root.sh' >> /root/.bashrc
echo 'fi' >> /root/.bashrc
source /data/docker/root.sh
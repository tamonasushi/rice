# -*- mode: ruby -*-
# vi: set ft=ruby :


$riceServer = <<SCRIPT

	yum update -y
	yum install -y epel-release
	yum group install -y "Development Tools"
	
	yum install -y vim unzip htop nfs-utils nfs-utils-lib git wget

	rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
	rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm

	yum install php56w php56w-opcache php56w-common php56w-mysql php56w-xml php56w-mbstring -y

	/etc/init.d/iptables stop
	chkconfig iptables off
	/etc/init.d/httpd start

	curl -sS https://getcomposer.org/installer | php
	chmod +x composer.phar
	mv composer.phar /usr/bin/composer

	mkdir -p /home/www/projects
	echo "#172.0.0.10:/home/victor/Projects /home/www/projects nfs rw,hard,intr 0 0" >> /etc/fstab

SCRIPT

VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

	############################ riceServer  ############################
	config.vm.define "riceServer" do |br|
		br.vm.box = "nrel/CentOS-6.5-x86_64"
		br.vm.hostname = "riceServer"
		br.vm.network :private_network, ip: "172.0.0.8"
		br.vm.provision :shell, inline: $riceServer
	end

	config.vm.provider :virtualbox do |vb|
		vb.customize ["modifyvm", :id, "--memory", "512"]
	end

end

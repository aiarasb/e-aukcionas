# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "forwarded_port", guest: 80, host: 8000
  config.vm.network "forwarded_port", guest: 30, host: 3000
  config.vm.synced_folder ".", "/srv", :nfs => true
  config.vm.network :private_network, ip: "10.15.10.22"
  config.vm.hostname = "e-aukcionas.dev"

  config.vm.provider :virtualbox do |v|
      v.customize [
          "modifyvm", :id,
          "--memory", 2048,
          "--cpus", 2,
          "--name", "e-aukcionas"
      ]
      v.customize [
         "setextradata", :id,
         "VBoxInternal2/SharedFoldersEnableSymlinksCreate/vagrant", "1"
      ]
  end

  config.vm.provision :shell, path: "bootstrap.sh"
end

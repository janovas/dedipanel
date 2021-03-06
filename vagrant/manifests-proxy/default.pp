user { 'dedipanel':
  ensure     => present,
  password   => '$6$oILnjmzn$5e3nIAGSPmWIuHH.wyeEQeQJG5Xl46khl6fztZV5GI/IkWxC5lsq.XAed3EbjMn0PL2CVcfsOBuqWUQIlklJ01',
  managehome => true,
  shell      => '/bin/bash',
}

class { 'apt':
  always_apt_update => true,
}

Class['::apt::update'] -> Package <|
    title != 'python-software-properties'
and title != 'software-properties-common'
|>

package { [
    'build-essential',
    'vim',
    'curl',
    'git-core',
    'nginx'
  ]:
  ensure => 'installed',
}

service { 'nginx':
  ensure => running,
  require => Package['nginx'],
}

file { 'default-nginx-disable':
  path => '/etc/nginx/sites-enabled/default',
  ensure => absent,
  require => Package['nginx'],
}

file { 'vagrant-nginx':
  path => '/etc/nginx/sites-available/dedipanel32.dev',
  ensure => file,
  require => Package['nginx'],
  source => 'puppet:////vagrant/manifests-proxy/files/dedipanel32.dev',
}

file { 'vagrant-nginx-enable':
  path => '/etc/nginx/sites-enabled/dedipanel32.dev',
  target => '/etc/nginx/sites-available/dedipanel32.dev',
  ensure => link,
  notify => Service['nginx'],
  require => [
    File['vagrant-nginx'],
    File['default-nginx-disable'],
  ],
}

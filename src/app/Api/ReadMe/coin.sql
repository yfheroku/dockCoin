#记录拉取记录的当前区块数/截止时间,供下次拉取使用
CREATE TABLE `symbol_recharge_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_num` int(15) DEFAULT '0',
  `symbol` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
#记录对应币种生成的钱包地址
CREATE TABLE `user_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT '' COMMENT '会员编号',
  `address` varchar(255) DEFAULT '' COMMENT '钱包地址',
  `symbol` varchar(20) DEFAULT '' COMMENT '币种',
  `w_time` int(10) DEFAULT '0',
  `secret` int(10) DEFAULT '' COMMENT '钱包私钥',
  `certificate` text COMMENT '记录额外凭证',
  PRIMARY KEY (`id`),
  KEY `s_name` (`symbol`,`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;
#记录充币和提币的交易记录
CREATE TABLE `user_hash` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(20) DEFAULT NULL,
  `amount` double(30,8) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `w_time` int(10) DEFAULT '0',
  `username` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 0确认中 1确认成功 -1交易失败',
  `destination` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT '''RMB''',
  `type` tinyint(1) DEFAULT '1' COMMENT '默认1 充值 2 提币',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`(191),`type`,`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
#user_nonce表,记录当前的以太坊(及其代币)交易的nonce值
#[nonce值作为交易的唯一标识,只要保证Nonce值唯一,就不会出现重发情况]
CREATE TABLE `user_nonce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nonce` varchar(255) NOT NULL DEFAULT '0.00000000',
  `gasPrice` varchar(50) NOT NULL DEFAULT '0.00000000',
  `gasLimit` varchar(50) NOT NULL DEFAULT '0.00000000',
  `to` varchar(125) NOT NULL DEFAULT '',
  `value` varchar(50) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `chainId` int(1) NOT NULL,
  `txid` int(11) DEFAULT '0',
  `txhash` varchar(255) DEFAULT '',
  `ten` varchar(255) DEFAULT '',
  `symbol` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;
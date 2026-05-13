// Created by Grigore Stefan <g_stefan@yahoo.com>
// Public domain (Unlicense) <http://unlicense.org>
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Unlicense

messageAction("clean-make [" + Project.name + "]");

Shell.removeDirRecursivelyForce("output");
Shell.remove("./temp/build.xyo.web.vendor.flag");
Shell.remove("./temp/build.xyo.web.library.vendor.flag");

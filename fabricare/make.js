// Created by Grigore Stefan <g_stefan@yahoo.com>
// Public domain (Unlicense) <http://unlicense.org>
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Unlicense

Fabricare.include("vendor");

// ---

messageAction("make [" + Project.name + "]");

Shell.mkdirRecursivelyIfNotExists("output");
Shell.mkdirRecursivelyIfNotExists("temp");

// ---

if (!Shell.fileExists("temp/build.xyo.web.vendor.flag")) {
    exitIf(Shell.system("7zr x vendor/xyo.web-"+Solution.vendor["xyo.web"]+".7z -aoa -ooutput"));
    Shell.filePutContents("temp/build.xyo.web.vendor.flag", "done");
};

if (!Shell.fileExists("temp/build.xyo.web.library.vendor.flag")) {
    exitIf(Shell.system("7zr x vendor/xyo.web.library-"+Solution.vendor["xyo.web.library"]+".7z -aoa -ooutput"));
    Shell.filePutContents("temp/build.xyo.web.library.vendor.flag", "done");
};

exitIf(!Shell.copyDirRecursively("source", "output"));

// ---

Fabricare.include("make.tailwind");


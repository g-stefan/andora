// Created by Grigore Stefan <g_stefan@yahoo.com>
// Public domain (Unlicense) <http://unlicense.org>
// SPDX-FileCopyrightText: 2026 Grigore Stefan <g_stefan@yahoo.com>
// SPDX-License-Identifier: Unlicense

messageAction("vendor tailwind");

Shell.mkdirRecursivelyIfNotExists("vendor");

if (Shell.fileExists("vendor/tailwind.7z")) {
	if (Shell.getFileSize("vendor/tailwind.7z") > 16) {
		return;
	};
	Shell.removeFile("vendor/tailwind.7z");
};

Shell.mkdirRecursivelyIfNotExists("vendor/tailwind");

runInPath("vendor/tailwind", function() {
	Shell.system("npm i tailwindcss @tailwindcss/postcss @tailwindcss/cli purgecss");
	Shell.system("npm i tailwindcss @tailwindcss/typography");
	Shell.system("npm i -D daisyui@latest");
	Shell.system("npm install -D tw-animate-css");
	exitIf(Shell.system("7z a -mx9 -mmt4 -r- -sse -w. -y -t7z ../tailwind.7z *"));
});

Shell.removeDirRecursively("vendor/tailwind");


# Mappers

Collection of light mappers meant for internal use by Ibexa packages only.

Given their use they should not rely on Repository or RepositoryServices as
that will lead to cyclic dependencies, they should only rely on SPI and other helpers.
